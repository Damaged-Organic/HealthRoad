<?php
// AppBundle/Controller/CRUD/SchoolController.php
namespace AppBundle\Controller\CRUD;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\RedirectResponse;

use JMS\DiExtraBundle\Annotation as DI;

use AppBundle\Service\Common\Utility\Exceptions\SearchException,
    AppBundle\Service\Common\Utility\Exceptions\PaginatorException;

use AppBundle\Controller\Utility\Traits\EntityFilter,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Entity\School\School,
    AppBundle\Form\Type\SchoolType,
    AppBundle\Security\Authorization\Voter\SchoolVoter,
    AppBundle\Service\Security\SchoolBoundlessAccess;

class SchoolController extends Controller implements UserRoleListInterface
{
    use EntityFilter;

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

    /** @DI\Inject("app.security.school_boundless_access") */
    private $_schoolBoundlessAccess;

    /**
     * @Method({"GET"})
     * @Route(
     *      "/school/{id}",
     *      name="school_read",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = null},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function readAction($id = NULL)
    {
        $repository = $this->_manager->getRepository('AppBundle:School\School');

        if( $id )
        {
            $school = $repository->find($id);

            if( !$school )
                throw $this->createNotFoundException("School identified by `id` {$id} not found");

            if( !$this->isGranted(SchoolVoter::SCHOOL_READ, $school) )
                throw $this->createAccessDeniedException('Access denied');

            $response = [
                'view' => 'AppBundle:Entity/School/CRUD:readItem.html.twig',
                'data' => ['school' => $school]
            ];

            $this->_breadcrumbs
                ->add('school_read')
                ->add('school_read', ['id' => $id], $this->_translator->trans('school_view', [], 'routes'))
            ;
        } else {
            if( !$this->_schoolBoundlessAccess->isGranted(SchoolBoundlessAccess::SCHOOL_READ) )
                throw $this->createAccessDeniedException('Access denied');

            try {
                $this->_entityResultsManager
                    ->setPageArgument($this->_paginator->getPageArgument())
                    ->setSearchArgument($this->_search->getSearchArgument())
                ;
            } catch(PaginatorException $ex) {
                throw $this->createNotFoundException('Invalid page argument');
            } catch(SearchException $ex) {
                return $this->redirectToRoute('school_read');
            }

            $schools = $this->_entityResultsManager->findRecords($repository);

            if( $schools === FALSE )
                return $this->redirectToRoute('school_read');

            $schools = $this->filterDeletedIfNotGranted(
                SchoolVoter::SCHOOL_READ, $schools
            );

            $response = [
                'view' => 'AppBundle:Entity/School/CRUD:readList.html.twig',
                'data' => ['schools' => $schools]
            ];

            $this->_breadcrumbs->add('school_read');
        }

        return $this->render($response['view'], $response['data']);
    }

    /**
     * @Method({"GET", "POST"})
     * @Route(
     *      "/school/create",
     *      name="school_create",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"}
     * )
     */
    public function createAction(Request $request)
    {
        if( !$this->_schoolBoundlessAccess->isGranted(SchoolBoundlessAccess::SCHOOL_CREATE) )
            throw $this->createAccessDeniedException('Access denied');

        $schoolType = new SchoolType($this->_schoolBoundlessAccess->isGranted(SchoolBoundlessAccess::SCHOOL_CREATE));

        $form = $this->createForm($schoolType, $school = new School, [
            'action' => $this->generateUrl('school_create')
        ]);

        $form->handleRequest($request);

        if( !($form->isValid()) ) {
            $this->_breadcrumbs->add('school_read')->add('school_create');

            return $this->render('AppBundle:Entity/School/CRUD:createItem.html.twig', [
                'form' => $form->createView()
            ]);
        } else {
            $this->_manager->persist($school);
            $this->_manager->flush();

            $this->_messages->markCreateSuccess();

            if( $form->has('create_and_return') && $form->get('create_and_return')->isClicked() ) {
                return $this->redirectToRoute('school_read');
            } else {
                return $this->redirectToRoute('school_update', [
                    'id' => $school->getId()
                ]);
            }
        }
    }

    /**
     * @Method({"GET", "POST"})
     * @Route(
     *      "/school/update/{id}",
     *      name="school_update",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function updateAction(Request $request, $id)
    {
        $school = $this->_manager->getRepository('AppBundle:School\School')->find($id);

        if( !$school )
            throw $this->createNotFoundException("School identified by `id` {$id} not found");

        if( !$this->isGranted(SchoolVoter::SCHOOL_UPDATE, $school) ) {
            return $this->redirectToRoute('school_read', [
                'id' => $school->getId()
            ]);
        }

        $schoolType = new SchoolType($this->_schoolBoundlessAccess->isGranted(SchoolBoundlessAccess::SCHOOL_CREATE));

        $form = $this->createForm($schoolType, $school, [
            'action' => $this->generateUrl('school_update', ['id' => $id])
        ]);

        $form->handleRequest($request);

        if( $form->isValid() )
        {
            $this->_manager->flush();

            $this->_messages->markUpdateSuccess();

            if( $form->has('update_and_return') && $form->get('update_and_return')->isClicked() ) {
                return $this->redirectToRoute('school_read');
            } else {
                return $this->redirectToRoute('school_update', [
                    'id' => $school->getId()
                ]);
            }
        }

        $this->_breadcrumbs->add('school_read')->add('school_update', ['id' => $id]);

        return $this->render('AppBundle:Entity/School/CRUD:updateItem.html.twig', [
            'form'   => $form->createView(),
            'school' => $school
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/school/delete/{id}",
     *      name="school_delete",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function deleteAction($id)
    {
        $school = $this->_manager->getRepository('AppBundle:School\School')->find($id);

        if( !$school )
            throw $this->createNotFoundException("School identified by `id` {$id} not found");

        if( !$this->isGranted(SchoolVoter::SCHOOL_DELETE, $school) )
            throw $this->createAccessDeniedException('Access denied');

        if( !$school->getPseudoDeleted() )
        {
            $school->setPseudoDeleted(TRUE);

            $this->_manager->flush();

            $this->_messages->markDeleteSuccess();
        } else {
            $school->setPseudoDeleted(FALSE);

            $this->_manager->flush();

            $this->_messages->markUnDeleteSuccess();
        }

        return new RedirectResponse($request->headers->get('referer'));
    }
}
