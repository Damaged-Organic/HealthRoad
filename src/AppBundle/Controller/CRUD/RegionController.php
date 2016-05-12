<?php
// AppBundle/Controller/CRUD/RegionController.php
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
    AppBundle\Entity\Region\Region,
    AppBundle\Form\Type\RegionType,
    AppBundle\Security\Authorization\Voter\RegionVoter,
    AppBundle\Service\Security\RegionBoundlessAccess;

class RegionController extends Controller implements UserRoleListInterface
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

    /** @DI\Inject("app.security.region_boundless_access") */
    private $_regionBoundlessAccess;

    /**
     * @Method({"GET"})
     * @Route(
     *      "/region/{id}",
     *      name="region_read",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = null},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function readAction($id = NULL)
    {
        $repository = $this->_manager->getRepository('AppBundle:Region\Region');

        if( $id )
        {
            $region = $repository->find($id);

            if( !$region )
                throw $this->createNotFoundException("Region identified by `id` {$id} not found");

            if( !$this->isGranted(RegionVoter::REGION_READ, $region) )
                throw $this->createAccessDeniedException('Access denied');

            $response = [
                'view' => 'AppBundle:Entity/Region/CRUD:readItem.html.twig',
                'data' => ['region' => $region]
            ];

            $this->_breadcrumbs
                ->add('region_read')
                ->add('region_read', ['id' => $id], $this->_translator->trans('region_view', [], 'routes'))
            ;
        } else {
            if( !$this->_regionBoundlessAccess->isGranted(RegionBoundlessAccess::REGION_READ) )
                throw $this->createAccessDeniedException('Access denied');

            try {
                $this->_entityResultsManager
                    ->setPageArgument($this->_paginator->getPageArgument())
                    ->setSearchArgument($this->_search->getSearchArgument())
                ;
            } catch(PaginatorException $ex) {
                throw $this->createNotFoundException('Invalid page argument');
            } catch(SearchException $ex) {
                return $this->redirectToRoute('region_read');
            }

            $regions = $this->_entityResultsManager->findRecords($repository);

            if( $regions === FALSE )
                return $this->redirectToRoute('region_read');

            $response = [
                'view' => 'AppBundle:Entity/Region/CRUD:readList.html.twig',
                'data' => ['regions' => $regions]
            ];

            $this->_breadcrumbs->add('region_read');
        }

        return $this->render($response['view'], $response['data']);
    }

    /**
     * @Method({"GET", "POST"})
     * @Route(
     *      "/region/create",
     *      name="region_create",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"}
     * )
     */
    public function createAction(Request $request)
    {
        if( !$this->_regionBoundlessAccess->isGranted(RegionBoundlessAccess::REGION_CREATE) )
            throw $this->createAccessDeniedException('Access denied');

        $regionType = new RegionType($this->_regionBoundlessAccess->isGranted(RegionBoundlessAccess::REGION_CREATE));

        $form = $this->createForm($regionType, $region = new Region, [
            'action' => $this->generateUrl('region_create')
        ]);

        $form->handleRequest($request);

        if( !($form->isValid()) ) {
            $this->_breadcrumbs->add('region_read')->add('region_create');

            return $this->render('AppBundle:Entity/Region/CRUD:createItem.html.twig', [
                'form' => $form->createView()
            ]);
        } else {
            $this->_manager->persist($region);
            $this->_manager->flush();

            $this->_messages->markCreateSuccess();

            if( $form->has('create_and_return') && $form->get('create_and_return')->isClicked() ) {
                return $this->redirectToRoute('region_read');
            } else {
                return $this->redirectToRoute('region_update', [
                    'id' => $region->getId()
                ]);
            }
        }
    }

    /**
     * @Method({"GET", "POST"})
     * @Route(
     *      "/region/update/{id}",
     *      name="region_update",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function updateAction(Request $request, $id)
    {
        $region = $this->_manager->getRepository('AppBundle:Region\Region')->find($id);

        if( !$region )
            throw $this->createNotFoundException("Region identified by `id` {$id} not found");

        if( !$this->isGranted(RegionVoter::REGION_UPDATE, $region) ) {
            return $this->redirectToRoute('region_read', [
                'id' => $region->getId()
            ]);
        }

        $regionType = new RegionType($this->_regionBoundlessAccess->isGranted(RegionBoundlessAccess::REGION_CREATE));

        $form = $this->createForm($regionType, $region, [
            'action' => $this->generateUrl('region_update', ['id' => $id])
        ]);

        $form->handleRequest($request);

        if( $form->isValid() )
        {
            $this->_manager->flush();

            $this->_messages->markUpdateSuccess();

            if( $form->has('update_and_return') && $form->get('update_and_return')->isClicked() ) {
                return $this->redirectToRoute('region_read');
            } else {
                return $this->redirectToRoute('region_update', [
                    'id' => $region->getId()
                ]);
            }
        }

        $this->_breadcrumbs->add('region_read')->add('region_update', ['id' => $id]);

        return $this->render('AppBundle:Entity/Region/CRUD:updateItem.html.twig', [
            'form'   => $form->createView(),
            'region' => $region
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/region/delete/{id}",
     *      name="region_delete",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function deleteAction($id)
    {
        $region = $this->_manager->getRepository('AppBundle:Region\Region')->find($id);

        if( !$region )
            throw $this->createNotFoundException("Region identified by `id` {$id} not found");

        if( !$this->isGranted(RegionVoter::REGION_DELETE, $region) )
            throw $this->createAccessDeniedException('Access denied');

        $this->_manager->remove($region);
        $this->_manager->flush();

        $this->_messages->markDeleteSuccess();

        return new RedirectResponse($request->headers->get('referer'));
    }
}
