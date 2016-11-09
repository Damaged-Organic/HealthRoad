<?php
// src/AppBundle/Controller/CRUD/BanknoteListController.php
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

use AppBundle\Entity\Banknote\BanknoteList,
    AppBundle\Security\Authorization\Voter\BanknoteListVoter,
    AppBundle\Service\Security\BanknoteListBoundlessAccess;

class BanknoteListController extends Controller
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

    /** @DI\Inject("app.security.banknote_list_boundless_access") */
    private $_banknoteListBoundlessAccess;

    /**
     * @Method({"GET"})
     * @Route(
     *      "/banknote_list/{id}",
     *      name="banknote_list_read",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = null},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function readAction($id = NULL)
    {
        $repository = $this->_manager->getRepository('AppBundle:Banknote\BanknoteList');

        if( $id )
        {
            $banknoteList = $repository->find($id);

            if( !$banknoteList )
                throw $this->createNotFoundException("Banknote List identified by `id` {$id} not found");

            if( !$this->isGranted(BanknoteListVoter::BANKNOTE_LIST_READ, $banknoteList) )
                throw $this->createAccessDeniedException('Access denied');

            $response = [
                'view' => 'AppBundle:Entity/BanknoteList/CRUD:readItem.html.twig',
                'data' => ['transaction' => $transaction]
            ];

            $this->_breadcrumbs->add('banknote_list_read')->add('banknote_list_read', ['id' => $id], $this->_translator->trans('banknote_list_view', [], 'routes'));
        } else {
            if( !$this->_banknoteListBoundlessAccess->isGranted(BanknoteListBoundlessAccess::BANKNOTE_LIST_READ) )
                throw $this->createAccessDeniedException('Access denied');

            try {
                $this->_entityResultsManager
                    ->setPageArgument($this->_paginator->getPageArgument())
                    ->setSearchArgument($this->_search->getSearchArgument())
                ;
            } catch(PaginatorException $ex) {
                throw $this->createNotFoundException('Invalid page argument');
            } catch(SearchException $ex) {
                return $this->redirectToRoute('banknote_list_read');
            }

            $banknoteLists = $this->_entityResultsManager->findRecords($repository);

            if( $banknoteLists === FALSE )
                return $this->redirectToRoute('banknote_list_read');

            $response = [
                'view' => 'AppBundle:Entity/BanknoteList/CRUD:readList.html.twig',
                'data' => ['banknoteLists' => $banknoteLists]
            ];

            $this->_breadcrumbs->add('banknote_list_read');
        }

        return $this->render($response['view'], $response['data']);
    }
}
