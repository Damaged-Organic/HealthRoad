<?php
// src/AppBundle/Controller/Binding/PurchaseServiceController.php
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
    AppBundle\Entity\PurchaseService\PurchaseService,
    AppBundle\Entity\Student\Student,
    AppBundle\Service\Security\PurchaseServiceBoundlessAccess;

class PurchaseServiceController extends Controller implements UserRoleListInterface
{
    use ClassOperationsTrait;

    /** @DI\Inject("request_stack") */
    private $_requestStack;

    /** @DI\Inject("doctrine.orm.entity_manager") */
    private $_manager;

    /** @DI\Inject("app.common.paginator") */
    private $_paginator;

    /** @DI\Inject("app.common.search") */
    private $_search;

    /** @DI\Inject("app.common.entity_results_manager") */
    private $_entityResultsManager;

    /** @DI\Inject("app.security.purchase_service_boundless_access") */
    private $_purchaseServiceBoundlessAccess;

    public function showAction($objectClass, $objectId)
    {
        if( !$this->_purchaseServiceBoundlessAccess->isGranted(PurchaseServiceBoundlessAccess::PURCHASE_SERVICE_READ) )
            throw $this->createAccessDeniedException('Access denied');

        switch(TRUE)
        {
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
            'objectClass' => $this->getObjectClassNameLower(new PurchaseService)
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

        $purchasesService = $this->_entityResultsManager->findRecords($object->getPurchasesService());

        if( $purchasesService === FALSE )
            return $this->redirectToRoute($route, $routeArguments);

        return $this->render('AppBundle:Entity/PurchaseService/Binding:show.html.twig', [
            'standalone'       => TRUE,
            'purchasesService' => $purchasesService,
            'object'           => $object
        ]);
    }
}
