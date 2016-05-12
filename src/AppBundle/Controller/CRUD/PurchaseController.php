<?php
// AppBundle/Controller/CRUD/PurchaseController.php
namespace AppBundle\Controller\CRUD;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\RedirectResponse;

use JMS\DiExtraBundle\Annotation as DI;

use AppBundle\Service\Common\Utility\Exceptions\SearchException,
    AppBundle\Service\Common\Utility\Exceptions\PaginatorException;

use AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Security\Authorization\Voter\PurchaseVoter,
    AppBundle\Service\Security\PurchaseBoundlessAccess;

class PurchaseController extends Controller implements UserRoleListInterface
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

    /** @DI\Inject("app.security.purchase_boundless_access") */
    private $_purchaseBoundlessAccess;

    /**
     * @Method({"GET"})
     * @Route(
     *      "/purchase/{id}",
     *      name="purchase_read",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = null},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function readAction($id = NULL)
    {
        $repository = $this->_manager->getRepository('AppBundle:Purchase\Purchase');

        if( $id )
        {
            $purchase = $repository->find($id);

            if( !$purchase )
                throw $this->createNotFoundException("Purchase identified by `id` {$id} not found");

            if( !$this->isGranted(PurchaseVoter::PURCHASE_READ, $purchase) )
                throw $this->createAccessDeniedException('Access denied');

            $response = [
                'view' => 'AppBundle:Entity/Purchase/CRUD:readItem.html.twig',
                'data' => ['purchase' => $purchase]
            ];

            $this->_breadcrumbs
                ->add('purchase_read')
                ->add('purchase_read', [], $this->_translator->trans('purchase_product_read', [], 'routes'))
                ->add('purchase_read', ['id' => $id], $this->_translator->trans('purchase_product_view', [], 'routes'))
            ;
        } else {
            if( !$this->_purchaseBoundlessAccess->isGranted(PurchaseBoundlessAccess::PURCHASE_READ) )
                throw $this->createAccessDeniedException('Access denied');

            try {
                $this->_entityResultsManager
                    ->setPageArgument($this->_paginator->getPageArgument())
                    ->setSearchArgument($this->_search->getSearchArgument())
                ;
            } catch(PaginatorException $ex) {
                throw $this->createNotFoundException('Invalid page argument');
            } catch(SearchException $ex) {
                return $this->redirectToRoute('purchase_read');
            }

            $purchases = $this->_entityResultsManager->findRecords($repository);

            if( $purchases === FALSE )
                return $this->redirectToRoute('purchase_read');

            $response = [
                'view' => 'AppBundle:Entity/Purchase/CRUD:readList.html.twig',
                'data' => ['purchases' => $purchases]
            ];

            $this->_breadcrumbs
                ->add('purchase_read')
                ->add('purchase_read', [], $this->_translator->trans('purchase_product_read', [], 'routes'))
            ;
        }

        return $this->render($response['view'], $response['data']);
    }
}
