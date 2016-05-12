<?php
// src/AppBundle/Controller/CRUD/PurchaseServiceController.php
namespace AppBundle\Controller\CRUD;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\RedirectResponse;

use JMS\DiExtraBundle\Annotation as DI;

use AppBundle\Service\Common\Utility\Exceptions\SearchException,
    AppBundle\Service\Common\Utility\Exceptions\PaginatorException;

use AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Security\Authorization\Voter\PurchaseServiceVoter,
    AppBundle\Service\Security\PurchaseServiceBoundlessAccess;

class PurchaseServiceController extends Controller implements UserRoleListInterface
{
    /** @DI\Inject("doctrine.orm.entity_manager") */
    private $_manager;

    /** @DI\Inject("translator") */
    private $_translator;

    /** @DI\Inject("app.common.breadcrumbs") */
    private $_breadcrumbs;

    /** @DI\Inject("app.common.paginator") */
    private $_paginator;

    /** @DI\Inject("app.common.search") */
    private $_search;

    /** @DI\Inject("app.common.entity_results_manager") */
    private $_entityResultsManager;

    /** @DI\Inject("app.security.purchase_service_boundless_access") */
    private $_purchaseServiceBoundlessAccess;

    /**
     * @Method({"GET"})
     * @Route(
     *      "/purchase_service/{id}",
     *      name="purchase_service_read",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = null},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function readAction($id = NULL)
    {
        $repository = $this->_manager->getRepository('AppBundle:PurchaseService\PurchaseService');

        if( $id )
        {
            $purchaseService = $repository->find($id);

            if( !$purchaseService )
                throw $this->createNotFoundException("Purchase Service identified by `id` {$id} not found");

            if( !$this->isGranted(PurchaseServiceVoter::PURCHASE_SERVICE_READ, $purchaseService) )
                throw $this->createAccessDeniedException('Access denied');

            $response = [
                'view' => 'AppBundle:Entity/PurchaseService/CRUD:readItem.html.twig',
                'data' => ['purchaseService' => $purchaseService]
            ];

            $this->_breadcrumbs
                ->add('purchase_read')
                ->add('purchase_service_read', [], $this->_translator->trans('purchase_service_read', [], 'routes'))
                ->add('purchase_service_read', ['id' => $id], $this->_translator->trans('purchase_service_view', [], 'routes'))
            ;
        } else {
            if( !$this->_purchaseServiceBoundlessAccess->isGranted(PurchaseServiceBoundlessAccess::PURCHASE_SERVICE_READ) )
                throw $this->createAccessDeniedException('Access denied');

            try {
                $this->_entityResultsManager
                    ->setPageArgument($this->_paginator->getPageArgument())
                    ->setSearchArgument($this->_search->getSearchArgument())
                ;
            } catch(PaginatorException $ex) {
                throw $this->createNotFoundException('Invalid page argument');
            } catch(SearchException $ex) {
                return $this->redirectToRoute('purchase_service_read');
            }

            $purchasesService = $this->_entityResultsManager->findRecords($repository);

            if( $purchasesService === FALSE )
                return $this->redirectToRoute('purchase_service_read');

            $response = [
                'view' => 'AppBundle:Entity/PurchaseService/CRUD:readList.html.twig',
                'data' => ['purchasesService' => $purchasesService]
            ];

            $this->_breadcrumbs
                ->add('purchase_read')
                ->add('purchase_service_read', [], $this->_translator->trans('purchase_service_read', [], 'routes'))
            ;
        }

        return $this->render($response['view'], $response['data']);
    }
}
