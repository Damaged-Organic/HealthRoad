<?php
// src/AppBundle/Controller/CRUD/PaymentReceiptController.php
namespace AppBundle\Controller\CRUD;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use JMS\DiExtraBundle\Annotation as DI;

use AppBundle\Service\Common\Utility\Exceptions\SearchException,
    AppBundle\Service\Common\Utility\Exceptions\PaginatorException;

use AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Security\Authorization\Voter\PaymentReceiptVoter,
    AppBundle\Service\Security\PaymentReceiptBoundlessAccess;

class PaymentReceiptController extends Controller implements UserRoleListInterface
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

    /** @DI\Inject("app.security.payment_receipt_boundless_access") */
    private $_paymentReceiptBoundlessAccess;

    /** @DI\Inject("app.payment.receipt.storage") */
    private $_paymentReceiptStorage;

    /**
     * @Method({"GET"})
     * @Route(
     *      "/payment_receipt/{id}",
     *      name="payment_receipt_read",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = null},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function readAction($id = NULL)
    {
        //If user returns to read view data in storage should be cleared
        $receipt = $this->_paymentReceiptStorage->clearReceipt();

        $repository = $this->_manager->getRepository('AppBundle:Payment\PaymentReceipt');

        if( $id )
        {
            $paymentReceipt = $repository->find($id);

            if( !$paymentReceipt )
                throw $this->createNotFoundException("Payment Receipt identified by `id` {$id} not found");

            if( !$this->isGranted(PaymentReceiptVoter::PAYMENT_RECEIPT_READ, $paymentReceipt) )
                throw $this->createAccessDeniedException('Access denied');

            $response = [
                'view' => 'AppBundle:Entity/PaymentReceipt/CRUD:readItem.html.twig',
                'data' => ['paymentReceipt' => $paymentReceipt]
            ];

            $this->_breadcrumbs
                ->add('payment_receipt_read')
                ->add('payment_receipt_read', ['id' => $id], $this->_translator->trans('payment_receipt_view', [], 'routes'))
            ;
        } else {
            if( !$this->_paymentReceiptBoundlessAccess->isGranted(PaymentReceiptBoundlessAccess::PAYMENT_RECEIPT_READ) )
                throw $this->createAccessDeniedException('Access denied');

            try {
                $this->_entityResultsManager
                    ->setPageArgument($this->_paginator->getPageArgument())
                    ->setSearchArgument($this->_search->getSearchArgument())
                ;
            } catch(PaginatorException $ex) {
                throw $this->createNotFoundException('Invalid page argument');
            } catch(SearchException $ex) {
                return $this->redirectToRoute('payment_receipt_read');
            }

            $paymentReceipts = $this->_entityResultsManager->findRecords($repository);

            if( $paymentReceipts === FALSE )
                return $this->redirectToRoute('payment_receipt_read');

            $response = [
                'view' => 'AppBundle:Entity/PaymentReceipt/CRUD:readList.html.twig',
                'data' => ['paymentReceipts' => $paymentReceipts]
            ];

            $this->_breadcrumbs->add('payment_receipt_read');
        }

        return $this->render($response['view'], $response['data']);
    }
}
