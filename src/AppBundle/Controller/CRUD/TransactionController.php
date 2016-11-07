<?php
// src/AppBundle/Controller/CRUD/TransactionController.php
namespace AppBundle\Controller\CRUD;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\RedirectResponse;

use JMS\DiExtraBundle\Annotation as DI;

use AppBundle\Service\Common\Utility\Exceptions\SearchException,
    AppBundle\Service\Common\Utility\Exceptions\PaginatorException;

use AppBundle\Controller\Utility\Traits\EntityFilter;

use AppBundle\Entity\Transaction\Transaction,
    AppBundle\Form\Type\TransactionType,
    AppBundle\Security\Authorization\Voter\TransactionVoter,
    AppBundle\Service\Security\TransactionBoundlessAccess;

class TransactionController extends Controller
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

    /** @DI\Inject("app.security.transaction_boundless_access") */
    private $_transactionBoundlessAccess;

    /**
     * @Method({"GET"})
     * @Route(
     *      "/transaction/{id}",
     *      name="transaction_read",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = null},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function readAction($id = NULL)
    {
        $repository = $this->_manager->getRepository('AppBundle:Transaction\Transaction');

        if( $id )
        {
            $transaction = $repository->find($id);

            if( !$transaction )
                throw $this->createNotFoundException("Transaction identified by `id` {$id} not found");

            if( !$this->isGranted(TransactionVoter::TRANSACTION_READ, $transaction) )
                throw $this->createAccessDeniedException('Access denied');

            $response = [
                'view' => 'AppBundle:Entity/Transaction/CRUD:readItem.html.twig',
                'data' => ['transaction' => $transaction]
            ];

            $this->_breadcrumbs->add('transaction_read')->add('transaction_read', ['id' => $id], $this->_translator->trans('transaction_view', [], 'routes'));
        } else {
            if( !$this->_transactionBoundlessAccess->isGranted(TransactionBoundlessAccess::TRANSACTION_READ) )
                throw $this->createAccessDeniedException('Access denied');

            try {
                $this->_entityResultsManager
                    ->setPageArgument($this->_paginator->getPageArgument())
                    ->setSearchArgument($this->_search->getSearchArgument())
                ;
            } catch(PaginatorException $ex) {
                throw $this->createNotFoundException('Invalid page argument');
            } catch(SearchException $ex) {
                return $this->redirectToRoute('transaction_read');
            }

            $transactions = $this->_entityResultsManager->findRecords($repository);

            if( $transactions === FALSE )
                return $this->redirectToRoute('transactions_read');

            $response = [
                'view' => 'AppBundle:Entity/Transaction/CRUD:readList.html.twig',
                'data' => ['transactions' => $transactions]
            ];

            $this->_breadcrumbs->add('transaction_read');
        }

        return $this->render($response['view'], $response['data']);
    }
}
