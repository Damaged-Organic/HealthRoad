<?php
// AppBundle/Controller/Binding/RegionController.php
namespace AppBundle\Controller\Binding;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\RedirectResponse,
    Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;

use JMS\DiExtraBundle\Annotation as DI;

use AppBundle\Service\Common\Utility\Exceptions\SearchException,
    AppBundle\Service\Common\Utility\Exceptions\PaginatorException;

use AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Controller\Utility\Traits\ClassOperationsTrait,
    AppBundle\Entity\Region\Region,
    AppBundle\Entity\Employee\Employee,
    AppBundle\Entity\Settlement\Settlement,
    AppBundle\Security\Authorization\Voter\EmployeeVoter,
    AppBundle\Security\Authorization\Voter\RegionVoter,
    AppBundle\Service\Security\RegionBoundlessAccess;

class RegionController extends Controller implements UserRoleListInterface
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

    public function showAction($objectClass, $objectId)
    {
        if( !$this->_regionBoundlessAccess->isGranted(RegionBoundlessAccess::REGION_READ) )
            throw $this->createAccessDeniedException('Access denied');

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Employee, $objectClass):
                $object = $this->_manager->getRepository('AppBundle:Employee\Employee')->find($objectId);

                if( !$object )
                    throw $this->createNotFoundException("Employee identified by `id` {$objectId} not found");

                // $regions = $this->_manager->getRepository('AppBundle:Region\Region')->findBy(['employee' => $object]);

                $action = [
                    'path'  => 'region_choose',
                    'voter' => EmployeeVoter::EMPLOYEE_BIND_REGION
                ];
            break;

            default:
                throw new NotAcceptableHttpException("Object not supported");
            break;
        }

        $route          = $this->_requestStack->getMasterRequest()->get('_route');
        $routeArguments = [
            'objectId'    => $objectId,
            'objectClass' => $this->getObjectClassNameLower(new Region)
        ];

        try {
            $this->_entityResultsManager
                ->setPageArgument($this->_paginator->getPageArgument())
                ->setSearchArgument($this->_search->getSearchArgument())
                ->setFindArgument(['employee' => $object])
            ;

            $this->_entityResultsManager->setRouteArguments($routeArguments);
        } catch(PaginatorException $ex) {
            throw $this->createNotFoundException('Invalid page argument');
        } catch(SearchException $ex) {
            return $this->redirectToRoute($route, $routeArguments);
        }

        $regions = $this->_entityResultsManager->findRecords(
            $this->_manager->getRepository('AppBundle:Region\Region')
        );

        if( $regions === FALSE )
            return $this->redirectToRoute($route, $routeArguments);

        return $this->render('AppBundle:Entity/Region/Binding:show.html.twig', [
            'standalone' => TRUE,
            'regions'    => $regions,
            'object'     => $object,
            'action'     => $action
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/region/update/{objectId}/bounded/{objectClass}",
     *      name="region_update_bounded",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "objectId" = "\d+", "objectClass" = "[a-z]+"}
     * )
     */
    public function boundedAction($objectId, $objectClass)
    {
        $region = $this->_manager->getRepository('AppBundle:Region\Region')->find($objectId);

        if( !$region )
            throw $this->createNotFoundException("Region identified by `id` {$objectId} not found");

        if( !$this->isGranted(RegionVoter::REGION_READ, $region) )
            throw $this->createAccessDeniedException('Access denied');

        $this->_breadcrumbs->add('region_read')->add('region_update', ['id' => $objectId], $this->_translator->trans('region_bounded', [], 'routes'));

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Settlement, $objectClass):
                $bounded = $this->forward('AppBundle:Binding\Settlement:show', [
                    'objectClass' => $this->getObjectClassName($region),
                    'objectId'    => $objectId
                ]);

                $this->_breadcrumbs->add('region_update_bounded',
                    [
                        'objectId'    => $objectId,
                        'objectClass' => $objectClass
                    ],
                    $this->_translator->trans('settlement_read', [], 'routes')
                );
            break;

            default:
                throw new NotAcceptableHttpException("Object not supported");
            break;
        }

        return $this->render('AppBundle:Entity/Region/Binding:bounded.html.twig', [
            'objectClass' => $objectClass,
            'bounded'     => $bounded->getContent(),
            'region'      => $region
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/region/choose_for/{objectClass}/{objectId}",
     *      name="region_choose",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "objectClass" = "[a-z]+", "objectId" = "\d+"}
     * )
     */
    public function chooseAction($objectClass, $objectId)
    {
        if( !$this->_regionBoundlessAccess->isGranted(RegionBoundlessAccess::REGION_BIND) )
            throw $this->createAccessDeniedException('Access denied');

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Employee, $objectClass):
                $employee = $object = $this->_manager->getRepository('AppBundle:Employee\Employee')->find($objectId);

                if( !$employee )
                    throw $this->createNotFoundException("Employee identified by `id` {$objectId} not found");

                if( !$this->isGranted(EmployeeVoter::EMPLOYEE_BIND_REGION, $employee) )
                    throw $this->createAccessDeniedException('Access denied: Regions can be bound to manager only');

                $path = 'employee_update_bounded';

                $this->_breadcrumbs->add('employee_read')->add('employee_update', ['id' => $objectId])->add('employee_update_bounded',
                    [
                        'objectId'    => $objectId,
                        'objectClass' => 'region'
                    ],
                    $this->_translator->trans('region_read', [], 'routes')
                );
            break;

            default:
                throw new NotAcceptableHttpException("Object not supported");
            break;
        }

        $routeArguments = [
            'objectId'    => $objectId,
            'objectClass' => $objectClass
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
            return $this->redirectToRoute('region_choose', $routeArguments);
        }

        $regions = $this->_entityResultsManager->findRecords(
            $this->_manager->getRepository('AppBundle:Region\Region')
        );

        if( $regions === FALSE )
            return $this->redirectToRoute('region_choose', $routeArguments);

        $this->_breadcrumbs->add('region_choose', $routeArguments);

        return $this->render('AppBundle:Entity/Region/Binding:choose.html.twig', [
            'path'    => $path,
            'regions' => $regions,
            'object'  => $object
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/region/bind/{targetId}/{objectClass}/{objectId}",
     *      name="region_bind",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "targetId" = "\d+", "objectClass" = "[a-z]+", "objectId" = "\d+"}
     * )
     */
    public function bindToAction(Request $request, $targetId, $objectClass, $objectId)
    {
        $region = $this->_manager->getRepository('AppBundle:Region\Region')->find($targetId);

        if( !$region )
            throw $this->createNotFoundException($this->_translator->trans('common.error.not_found', [], 'responses'));

        if( !$this->isGranted(RegionVoter::REGION_BIND, $region) )
            throw $this->createAccessDeniedException($this->_translator->trans('common.error.forbidden', [], 'responses'));

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Employee, $objectClass):
                $employee = $this->_manager->getRepository('AppBundle:Employee\Employee')->find($objectId);

                if( !$employee )
                    throw $this->createNotFoundException($this->_translator->trans('common.error.not_found', [], 'responses'));

                if( !$this->isGranted(EmployeeVoter::EMPLOYEE_BIND_REGION, $employee) )
                    throw $this->createAccessDeniedException($this->_translator->trans('common.error.forbidden', [], 'responses'));

                $employee->addRegion($region);

                $this->_manager->persist($employee);
            break;

            default:
                throw new NotAcceptableHttpException($this->_translator->trans('bind.error.not_boundalbe', [], 'responses'));
            break;
        }

        $this->_manager->flush();

        $this->_messages->markBindSuccess(
            $this->_translator->trans('bind.success.region', [], 'responses')
        );

        return new RedirectResponse($request->headers->get('referer'));
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/region/unbind/{targetId}/{objectClass}/{objectId}",
     *      name="region_unbind",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "targetId" = "\d+", "objectClass" = "[a-z]+", "objectId" = "\d+"}
     * )
     */
    public function unbindFromAction(Request $request, $targetId, $objectClass, $objectId)
    {
        $region = $this->_manager->getRepository('AppBundle:Region\Region')->find($targetId);

        if( !$region )
            throw $this->createNotFoundException($this->_translator->trans('common.error.not_found', [], 'responses'));

        if( !$this->isGranted(RegionVoter::REGION_BIND, $region) )
            throw $this->createAccessDeniedException($this->_translator->trans('common.error.forbidden', [], 'responses'));

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Employee, $objectClass):
                $region->setEmployee(NULL);
            break;

            default:
                throw new NotAcceptableHttpException($this->_translator->trans('bind.error.not_unboundalbe', [], 'responses'));
            break;
        }

        $this->_manager->flush();

        $this->_messages->markUnbindSuccess(
            $this->_translator->trans('unbind.success.region', [], 'responses')
        );

        return new RedirectResponse($request->headers->get('referer'));
    }
}
