<?php
// src/AppBundle/Controller/Binding/TransactionController.php
namespace AppBundle\Controller\Binding;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;

use JMS\DiExtraBundle\Annotation as DI;

use AppBundle\Service\Common\Utility\Exceptions\SearchException,
    AppBundle\Service\Common\Utility\Exceptions\PaginatorException;

use AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Controller\Utility\Traits\ClassOperationsTrait,
    AppBundle\Entity\Transaction\Transaction,
    AppBundle\Entity\VendingMachine\VendingMachine,
    AppBundle\Entity\Student\Student,
    AppBundle\Entity\Banknote\BanknoteList,
    AppBundle\Security\Authorization\Voter\TransactionVoter,
    AppBundle\Service\Security\TransactionBoundlessAccess;

class TransactionController extends Controller implements UserRoleListInterface
{
    use ClassOperationsTrait;

    /** @DI\Inject("request_stack") */
    private $_requestStack;

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

    /** @DI\Inject("app.security.transaction_boundless_access") */
    private $_transactionBoundlessAccess;

    public function showAction($objectClass, $objectId)
    {
        if( !$this->_transactionBoundlessAccess->isGranted(TransactionBoundlessAccess::TRANSACTION_READ) )
            throw $this->createAccessDeniedException('Access denied');

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new VendingMachine, $objectClass):
                $object = $this->_manager->getRepository('AppBundle:VendingMachine\VendingMachine')->find($objectId);

                if( !$object )
                    throw $this->createNotFoundException("Vending Machine identified by `id` {$objectId} not found");
            break;

            case $this->compareObjectClassNameToString(new Student, $objectClass):
                $object = $this->_manager->getRepository('AppBundle:Student\Student')->find($objectId);

                if( !$object )
                    throw $this->createNotFoundException("Student identified by `id` {$objectId} not found");
            break;

            default:
                throw new NotAcceptableHttpException("Object not supported");
            break;
        }

        $route          = $this->_requestStack->getMasterRequest()->get('_route');
        $routeArguments = [
            'objectId'    => $objectId,
            'objectClass' => $this->getObjectClassNameLower(new Transaction)
        ];

        try {
            $this->_entityResultsManager
                ->setPageArgument($this->_paginator->getPageArgument())
                ->setSearchArgument($this->_search->getSearchArgument())
            ;

            $this->_entityResultsManager->setRouteArguments($routeArguments);
        } catch(PaginatorException $ex) {
            throw $this->createNotFoundException('Invalid page argument');
        } catch(SearchException $ex) {
            return $this->redirectToRoute($route, $routeArguments);
        }

        $transactions = $this->_entityResultsManager->findRecords($object->getTransactions());

        if( $transactions === FALSE )
            return $this->redirectToRoute($route, $routeArguments);

        return $this->render('AppBundle:Entity/Transaction/Binding:show.html.twig', [
            'standalone'   => TRUE,
            'transactions' => $transactions,
            'object'       => $object,
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/transaction/update/{objectId}/bounded/{objectClass}",
     *      name="transaction_update_bounded",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "objectId" = "\d+", "objectClass" = "[a-z]+"}
     * )
     */
    public function boundedAction($objectId, $objectClass)
    {
        $transaction = $this->_manager->getRepository('AppBundle:Transaction\Transaction')->find($objectId);

        if( !$transaction )
            throw $this->createNotFoundException("Transaction identified by `id` {$objectId} not found");

        if( !$this->isGranted(TransactionVoter::TRANSACTION_READ, $transaction) )
            throw $this->createAccessDeniedException('Access denied');

        $this->_breadcrumbs->add('transaction_read'); //->add('transaction_update', ['id' => $objectId], $this->_translator->trans('transaction_bounded', [], 'routes'));

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new BanknoteList, $objectClass):
                $bounded = $this->forward('AppBundle:Binding\BanknoteList:show', [
                    'objectClass' => $this->getObjectClassName($transaction),
                    'objectId'    => $objectId
                ]);

                $this->_breadcrumbs->add('transaction_update_bounded',
                    [
                        'objectId'    => $objectId,
                        'objectClass' => $objectClass
                    ],
                    $this->_translator->trans('banknote_list_read', [], 'routes')
                );
            break;

            default:
                throw new NotAcceptableHttpException("Object not supported");
            break;
        }

        return $this->render('AppBundle:Entity/Transaction/Binding:bounded.html.twig', [
            'objectClass' => $objectClass,
            'bounded'     => $bounded->getContent(),
            'transaction' => $transaction,
        ]);
    }
}
