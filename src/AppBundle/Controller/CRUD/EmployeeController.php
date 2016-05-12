<?php
// AppBundle/Controller/CRUD/EmployeeController.php
namespace AppBundle\Controller\CRUD;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\RedirectResponse;

use JMS\DiExtraBundle\Annotation as DI;

use AppBundle\Service\Common\Utility\Exceptions\SearchException,
    AppBundle\Service\Common\Utility\Exceptions\PaginatorException;

use AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Entity\Employee\Employee,
    AppBundle\Form\Type\EmployeeType,
    AppBundle\Security\Authorization\Voter\EmployeeVoter,
    AppBundle\Service\Security\EmployeeBoundlessAccess;

class EmployeeController extends Controller implements UserRoleListInterface
{
    /** @DI\Inject("doctrine.orm.entity_manager") */
    private $_manager;

    /** @DI\Inject("translator") */
    private $_translator;

    /** @DI\Inject("app.common.breadcrumbs") */
    private $_breadcrumbs;

    /** @DI\Inject("app.common.messages") */
    private $_messages;

    /** @DI\Inject("app.common.paginator") */
    private $_paginator;

    /** @DI\Inject("app.common.search") */
    private $_search;

    /** @DI\Inject("app.common.entity_results_manager") */
    private $_entityResultsManager;

    /** @DI\Inject("app.security.employee_boundless_access") */
    private $_employeeBoundlessAccess;

    /**
     * @Method({"GET"})
     * @Route(
     *      "/employee/{id}",
     *      name="employee_read",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = null},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function readAction($id = NULL)
    {
        $repository = $this->_manager->getRepository('AppBundle:Employee\Employee');

        if( $id )
        {
            $employee = $repository->find($id);

            if( !$employee )
                throw $this->createNotFoundException("Employee identified by `id` {$id} not found");

            if( !$this->isGranted(EmployeeVoter::EMPLOYEE_READ, $employee) )
                throw $this->createAccessDeniedException('Access denied');

            $response = [
                'view' => 'AppBundle:Entity/Employee/CRUD:readItem.html.twig',
                'data' => ['employee' => $employee]
            ];

            $this->_breadcrumbs
                ->add('employee_read')
                ->add('employee_read', ['id' => $id], $this->_translator->trans('employee_view', [], 'routes'))
            ;
        } else {
            if( !$this->_employeeBoundlessAccess->isGranted(EmployeeBoundlessAccess::EMPLOYEE_READ) )
                throw $this->createAccessDeniedException('Access denied');

            try {
                $this->_entityResultsManager
                    ->setPageArgument($this->_paginator->getPageArgument())
                    ->setSearchArgument($this->_search->getSearchArgument())
                ;
            } catch(PaginatorException $ex) {
                throw $this->createNotFoundException('Invalid page argument');
            } catch(SearchException $ex) {
                return $this->redirectToRoute('employee_read');
            }

            $employees = $this->_entityResultsManager->findRecords($repository);

            if( $employees === FALSE )
                return $this->redirectToRoute('employee_read');

            $response = [
                'view' => 'AppBundle:Entity/Employee/CRUD:readList.html.twig',
                'data' => ['employees' => $employees]
            ];

            $this->_breadcrumbs->add('employee_read');
        }

        return $this->render($response['view'], $response['data']);
    }

    /**
     * @Method({"GET", "POST"})
     * @Route(
     *      "/employee/create",
     *      name="employee_create",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"}
     * )
     */
    public function createAction(Request $request)
    {
        if( !$this->_employeeBoundlessAccess->isGranted(EmployeeBoundlessAccess::EMPLOYEE_CREATE) )
            throw $this->createAccessDeniedException('Access denied');

        $employeeType = new EmployeeType(
            $this->_translator,
            $this->_employeeBoundlessAccess->isGranted(EmployeeBoundlessAccess::EMPLOYEE_CREATE)
        );

        $form = $this->createForm($employeeType, $employee = new Employee, [
            'validation_groups' => ['Employee', 'Strict', 'Create'],
            'action'            => $this->generateUrl('employee_create')
        ]);

        $form->handleRequest($request);

        if( !($form->isValid()) ) {
            $this->_breadcrumbs->add('employee_read')->add('employee_create');

            return $this->render('AppBundle:Entity/Employee/CRUD:createItem.html.twig', [
                'form' => $form->createView()
            ]);
        } else {
            if( !$this->isGranted(EmployeeVoter::EMPLOYEE_CREATE, $employee) )
                throw $this->createAccessDeniedException('Access denied');

            $encodedPassword = $this
                ->container->get('security.password_encoder')
                ->encodePassword($employee, $employee->getPassword())
            ;

            // Set employee's password
            $employee->setPassword($encodedPassword);

            $this->_manager->persist($employee);
            $this->_manager->flush();

            $this->_messages->markCreateSuccess();

            if( $form->has('create_and_return') && $form->get('create_and_return')->isClicked() ) {
                return $this->redirectToRoute('employee_read');
            } else {
                return $this->redirectToRoute('employee_update', [
                    'id' => $employee->getId()
                ]);
            }
        }
    }

    /**
     * @Method({"GET", "POST"})
     * @Route(
     *      "/employee/update/{id}",
     *      name="employee_update",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function updateAction(Request $request, $id)
    {
        $employee = $this->_manager->getRepository('AppBundle:Employee\Employee')->find($id);

        if( !$employee )
            throw $this->createNotFoundException("Employee identified by `id` {$id} not found");

        if( !$this->isGranted(EmployeeVoter::EMPLOYEE_UPDATE, $employee) ) {
            return $this->redirectToRoute('employee_read', [
                'id' => $employee->getId()
            ]);
        }

        $employeeType = new EmployeeType(
            $this->_translator,
            $this->_employeeBoundlessAccess->isGranted(EmployeeBoundlessAccess::EMPLOYEE_CREATE),
            $this->isGranted(EmployeeVoter::EMPLOYEE_UPDATE_SYSTEM, $employee)
        );

        $form = $this->createForm($employeeType, $employee, [
            'validation_groups' => ['Employee', 'Strict', 'Update'],
            'action'            => $this->generateUrl('employee_update', ['id' => $id])
        ]);

        $form->handleRequest($request);

        if( $form->isValid() )
        {
            if( $form->has('password') && $form->get('password')->getData() )
            {
                $encodedPassword = $this
                    ->container->get('security.password_encoder')
                    ->encodePassword($employee, $employee->getPassword());

                $employee->setPassword($encodedPassword);
            }

            $this->_manager->flush();

            $this->_messages->markUpdateSuccess();

            if( $form->has('update_and_return') && $form->get('update_and_return')->isClicked() ) {
                return $this->redirectToRoute('employee_read');
            } else {
                return $this->redirectToRoute('employee_update', [
                    'id' => $employee->getId()
                ]);
            }
        }

        $this->_breadcrumbs->add('employee_read')->add('employee_update', ['id' => $id]);

        return $this->render('AppBundle:Entity/Employee/CRUD:updateItem.html.twig', [
            'form'     => $form->createView(),
            'employee' => $employee
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/employee/delete/{id}",
     *      name="employee_delete",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function deleteAction($id)
    {
        $employee = $this->_manager->getRepository('AppBundle:Employee\Employee')->find($id);

        if( !$employee )
            throw $this->createNotFoundException("Employee identified by `id` {$id} not found");

        if( !$this->isGranted(EmployeeVoter::EMPLOYEE_DELETE, $employee) )
            throw $this->createAccessDeniedException('Access denied');

        $this->_manager->remove($employee);
        $this->_manager->flush();

        $this->_messages->markDeleteSuccess();

        return new RedirectResponse($request->headers->get('referer'));
    }
}
