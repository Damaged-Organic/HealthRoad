<?php
// AppBundle/Controller/CRUD/SettlementController.php
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
    AppBundle\Entity\Settlement\Settlement,
    AppBundle\Form\Type\SettlementType,
    AppBundle\Security\Authorization\Voter\SettlementVoter,
    AppBundle\Service\Security\SettlementBoundlessAccess;

class SettlementController extends Controller implements UserRoleListInterface
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

    /** @DI\Inject("app.security.settlement_boundless_access") */
    private $_settlementBoundlessAccess;

    /**
     * @Method({"GET"})
     * @Route(
     *      "/settlement/{id}",
     *      name="settlement_read",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = null},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function readAction($id = NULL)
    {
        $repository = $this->_manager->getRepository('AppBundle:Settlement\Settlement');

        if( $id )
        {
            $settlement = $repository->find($id);

            if( !$settlement )
                throw $this->createNotFoundException("Settlement identified by `id` {$id} not found");

            if( !$this->isGranted(SettlementVoter::SETTLEMENT_READ, $settlement) )
                throw $this->createAccessDeniedException('Access denied');

            $response = [
                'view' => 'AppBundle:Entity/Settlement/CRUD:readItem.html.twig',
                'data' => ['settlement' => $settlement]
            ];

            $this->_breadcrumbs
                ->add('settlement_read')
                ->add('settlement_read', ['id' => $id], $this->_translator->trans('settlement_view', [], 'routes'))
            ;
        } else {
            if( !$this->_settlementBoundlessAccess->isGranted(SettlementBoundlessAccess::SETTLEMENT_READ) )
                throw $this->createAccessDeniedException('Access denied');

            try {
                $this->_entityResultsManager
                    ->setPageArgument($this->_paginator->getPageArgument())
                    ->setSearchArgument($this->_search->getSearchArgument())
                ;
            } catch(PaginatorException $ex) {
                throw $this->createNotFoundException('Invalid page argument');
            } catch(SearchException $ex) {
                return $this->redirectToRoute('settlement_read');
            }

            $settlements = $this->_entityResultsManager->findRecords($repository);

            if( $settlements === FALSE )
                return $this->redirectToRoute('settlement_read');

            $response = [
                'view' => 'AppBundle:Entity/Settlement/CRUD:readList.html.twig',
                'data' => ['settlements' => $settlements]
            ];

            $this->_breadcrumbs->add('settlement_read');
        }

        return $this->render($response['view'], $response['data']);
    }

    /**
     * @Method({"GET", "POST"})
     * @Route(
     *      "/settlement/create",
     *      name="settlement_create",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"}
     * )
     */
    public function createAction(Request $request)
    {
        if( !$this->_settlementBoundlessAccess->isGranted(SettlementBoundlessAccess::SETTLEMENT_CREATE) )
            throw $this->createAccessDeniedException('Access denied');

        $settlementType = new SettlementType(
            $this->_settlementBoundlessAccess->isGranted(SettlementBoundlessAccess::SETTLEMENT_CREATE)
        );

        $form = $this->createForm($settlementType, $settlement = new Settlement, [
            'action' => $this->generateUrl('settlement_create')
        ]);

        $form->handleRequest($request);

        if( !($form->isValid()) ) {
            $this->_breadcrumbs->add('settlement_read')->add('settlement_create');

            return $this->render('AppBundle:Entity/Settlement/CRUD:createItem.html.twig', [
                'form' => $form->createView()
            ]);
        } else {
            $this->_manager->persist($settlement);
            $this->_manager->flush();

            $this->_messages->markCreateSuccess();

            if( $form->has('create_and_return') && $form->get('create_and_return')->isClicked() ) {
                return $this->redirectToRoute('settlement_read');
            } else {
                return $this->redirectToRoute('settlement_update', [
                    'id' => $settlement->getId()
                ]);
            }
        }
    }

    /**
     * @Method({"GET", "POST"})
     * @Route(
     *      "/settlement/update/{id}",
     *      name="settlement_update",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function updateAction(Request $request, $id)
    {
        $settlement = $this->_manager->getRepository('AppBundle:Settlement\Settlement')->find($id);

        if( !$settlement )
            throw $this->createNotFoundException("Settlement identified by `id` {$id} not found");

        if( !$this->isGranted(SettlementVoter::SETTLEMENT_UPDATE, $settlement) ) {
            return $this->redirectToRoute('settlement_read', [
                'id' => $settlement->getId()
            ]);
        }

        $settlementType = new SettlementType(
            $this->_settlementBoundlessAccess->isGranted(SettlementBoundlessAccess::SETTLEMENT_CREATE)
        );

        $form = $this->createForm($settlementType, $settlement, [
            'action' => $this->generateUrl('settlement_update', ['id' => $id])
        ]);

        $form->handleRequest($request);

        if( $form->isValid() )
        {
            $this->_manager->flush();

            $this->_messages->markUpdateSuccess();

            if( $form->has('update_and_return') && $form->get('update_and_return')->isClicked() ) {
                return $this->redirectToRoute('settlement_read');
            } else {
                return $this->redirectToRoute('settlement_update', [
                    'id' => $settlement->getId()
                ]);
            }
        }

        $this->_breadcrumbs->add('settlement_read')->add('settlement_update', ['id' => $id]);

        return $this->render('AppBundle:Entity/Settlement/CRUD:updateItem.html.twig', [
            'form'       => $form->createView(),
            'settlement' => $settlement
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/settlement/delete/{id}",
     *      name="settlement_delete",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function deleteAction($id)
    {
        $settlement = $this->_manager->getRepository('AppBundle:Settlement\Settlement')->find($id);

        if( !$settlement )
            throw $this->createNotFoundException("Settlement identified by `id` {$id} not found");

        if( !$this->isGranted(SettlementVoter::SETTLEMENT_DELETE, $settlement) )
            throw $this->createAccessDeniedException('Access denied');

        $this->_manager->remove($settlement);
        $this->_manager->flush();

        $this->_messages->markDeleteSuccess();

        return new RedirectResponse($request->headers->get('referer'));
    }
}
