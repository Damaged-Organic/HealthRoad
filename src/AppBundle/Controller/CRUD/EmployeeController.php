<?php
// AppBundle/Controller/CRUD/EmployeeController.php
namespace AppBundle\Controller\CRUD;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request;

use AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Entity\Employee\Employee,
    AppBundle\Form\Type\EmployeeType,
    AppBundle\Security\Authorization\Voter\EmployeeVoter,
    AppBundle\Service\Security\EmployeeBoundlessAccess;

class EmployeeController extends Controller implements UserRoleListInterface
{
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
    public function readAction($id)
    {
        $_manager = $this->getDoctrine()->getManager();

        $_employeeBoundlessAccess = $this->get('app.security.employee_boundless_access');

        if( $id )
        {
            $employee = $_manager->getRepository('AppBundle:Employee\Employee')->find($id);

            if( !$employee )
                throw $this->createNotFoundException("Employee identified by `id` {$id} not found");

            if( !$this->isGranted(EmployeeVoter::EMPLOYEE_READ, $employee) )
                throw $this->createAccessDeniedException('Access denied');

            $response = [
                'view' => 'AppBundle:Entity/Employee/CRUD:readItem.html.twig',
                'data' => ['employee' => $employee]
            ];
        } else {
            if( !$_employeeBoundlessAccess->isGranted(EmployeeBoundlessAccess::EMPLOYEE_READ) )
                throw $this->createAccessDeniedException('Access denied');

            $employees = $_manager->getRepository('AppBundle:Employee\Employee')->findAll();

            $response = [
                'view' => 'AppBundle:Entity/Employee/CRUD:readList.html.twig',
                'data' => ['employees' => $employees]
            ];
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
        $_employeeBoundlessAccess = $this->get('app.security.employee_boundless_access');

        if( !$_employeeBoundlessAccess->isGranted(EmployeeBoundlessAccess::EMPLOYEE_CREATE) )
            throw $this->createAccessDeniedException('Access denied');

        $employeeType = new EmployeeType($_employeeBoundlessAccess->isGranted(EmployeeBoundlessAccess::EMPLOYEE_CREATE));

        $form = $this->createForm($employeeType, $employee = new Employee, [
            'validation_groups' => ['Employee', 'Strict', 'Create']
        ]);

        $form->handleRequest($request);

        if( !($form->isValid()) ) {
            return $this->render('AppBundle:Entity/Employee/CRUD:createItem.html.twig', [
                'form' => $form->createView()
            ]);
        } else {
            $_manager = $this->getDoctrine()->getManager();

            if( !$this->isGranted(EmployeeVoter::EMPLOYEE_CREATE, $employee) )
                throw $this->createAccessDeniedException('Access denied');

            $encodedPassword = $this
                ->container->get('security.password_encoder')
                ->encodePassword($employee, $employee->getPassword())
            ;

            $employee->setPassword($encodedPassword);

            $_manager->persist($employee);
            $_manager->flush();

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
        $_manager = $this->getDoctrine()->getManager();

        $_employeeBoundlessAccess = $this->get('app.security.employee_boundless_access');

        $employee = $_manager->getRepository('AppBundle:Employee\Employee')->find($id);

        if( !$employee )
            throw $this->createNotFoundException("Employee identified by `id` {$id} not found");

        if( !$this->isGranted(EmployeeVoter::EMPLOYEE_UPDATE, $employee) ) {
            return $this->redirectToRoute('employee_read', [
                'id' => $employee->getId()
            ]);
        }

        $employeeType = new EmployeeType(
            $_employeeBoundlessAccess->isGranted(EmployeeBoundlessAccess::EMPLOYEE_CREATE),
            $this->isGranted(EmployeeVoter::EMPLOYEE_UPDATE_SYSTEM, $employee)
        );

        $form = $this->createForm($employeeType, $employee, [
            'validation_groups' => ['Employee', 'Strict', 'Update']
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

            $_manager->flush();

            if( $form->has('update_and_return') && $form->get('update_and_return')->isClicked() ) {
                return $this->redirectToRoute('employee_read');
            } else {
                return $this->redirectToRoute('employee_update', [
                    'id' => $employee->getId()
                ]);
            }
        }

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
        $_manager = $this->getDoctrine()->getManager();

        $employee = $_manager->getRepository('AppBundle:Employee\Employee')->find($id);

        if( !$employee )
            throw $this->createNotFoundException("Employee identified by `id` {$id} not found");

        if( !$this->isGranted(EmployeeVoter::EMPLOYEE_DELETE, $employee) )
            throw $this->createAccessDeniedException('Access denied');

        $_manager->remove($employee);
        $_manager->flush();

        return $this->redirectToRoute('employee_read');
    }
}