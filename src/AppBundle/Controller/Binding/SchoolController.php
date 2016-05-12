<?php
// AppBundle/Controller/Binding/SchoolController.php
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

use AppBundle\Controller\Utility\Traits\EntityFilter,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Controller\Utility\Traits\ClassOperationsTrait,
    AppBundle\Entity\School\School,
    AppBundle\Entity\Employee\Employee,
    AppBundle\Entity\Settlement\Settlement,
    AppBundle\Entity\Student\Student,
    AppBundle\Entity\VendingMachine\VendingMachine,
    AppBundle\Security\Authorization\Voter\EmployeeVoter,
    AppBundle\Security\Authorization\Voter\SchoolVoter,
    AppBundle\Security\Authorization\Voter\SettlementVoter,
    AppBundle\Service\Security\SchoolBoundlessAccess;

class SchoolController extends Controller implements UserRoleListInterface
{
    use ClassOperationsTrait, EntityFilter;

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

    /** @DI\Inject("app.security.school_boundless_access") */
    private $_schoolBoundlessAccess;

    public function showAction($objectClass, $objectId)
    {
        if( !$this->_schoolBoundlessAccess->isGranted(SchoolBoundlessAccess::SCHOOL_READ) )
            throw $this->createAccessDeniedException('Access denied');

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Employee, $objectClass):
                $object = $this->_manager->getRepository('AppBundle:Employee\Employee')->find($objectId);

                if( !$object )
                    throw $this->createNotFoundException("Employee identified by `id` {$objectId} not found");

                $action = [
                    'path'  => 'school_choose',
                    'voter' => EmployeeVoter::EMPLOYEE_BIND_SCHOOL
                ];
            break;

            case $this->compareObjectClassNameToString(new Settlement, $objectClass):
                $object = $this->_manager->getRepository('AppBundle:Settlement\Settlement')->find($objectId);

                if( !$object )
                    throw $this->createNotFoundException("Settlement identified by `id` {$objectId} not found");

                $action = [
                    'path'  => 'school_choose',
                    'voter' => SettlementVoter::SETTLEMENT_BIND
                ];
            break;

            default:
                throw new NotAcceptableHttpException("Object not supported");
            break;
        }

        $route          = $this->_requestStack->getMasterRequest()->get('_route');
        $routeArguments = [
            'objectId'    => $objectId,
            'objectClass' => $this->getObjectClassNameLower(new School)
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

        $schools = $this->_entityResultsManager->findRecords($object->getSchools());

        if( $schools === FALSE )
            return $this->redirectToRoute($route, $routeArguments);

        $schools = $this->filterDeletedIfNotGranted(
            SchoolVoter::SCHOOL_READ, $schools
        );

        return $this->render('AppBundle:Entity/School/Binding:show.html.twig', [
            'standalone' => TRUE,
            'schools'    => $schools,
            'object'     => $object,
            'action'     => $action
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/school/update/{objectId}/bounded/{objectClass}",
     *      name="school_update_bounded",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "objectId" = "\d+", "objectClass" = "[a-z]+"}
     * )
     */
    public function boundedAction($objectId, $objectClass)
    {
        $school = $this->_manager->getRepository('AppBundle:School\School')->find($objectId);

        if( !$school )
            throw $this->createNotFoundException("School identified by `id` {$objectId} not found");

        if( !$this->isGranted(SchoolVoter::SCHOOL_READ, $school) )
            throw $this->createAccessDeniedException('Access denied');

        $this->_breadcrumbs->add('school_read')->add('school_update', ['id' => $objectId], $this->_translator->trans('school_bounded', [], 'routes'));

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new VendingMachine, $objectClass):
                $bounded = $this->forward('AppBundle:Binding\VendingMachine:show', [
                    'objectClass' => $this->getObjectClassName($school),
                    'objectId'    => $objectId
                ]);

                $this->_breadcrumbs->add('school_update_bounded',
                    [
                        'objectId'    => $objectId,
                        'objectClass' => $objectClass
                    ],
                    $this->_translator->trans('vending_machine_read', [], 'routes')
                );
            break;

            case $this->compareObjectClassNameToString(new Student, $objectClass):
                $bounded = $this->forward('AppBundle:Binding\Student:show', [
                    'objectClass' => $this->getObjectClassName($school),
                    'objectId'    => $objectId
                ]);

                $this->_breadcrumbs->add('school_update_bounded',
                    [
                        'objectId'    => $objectId,
                        'objectClass' => $objectClass
                    ],
                    $this->_translator->trans('student_read', [], 'routes')
                );
            break;

            default:
                throw new NotAcceptableHttpException("Object not supported");
            break;
        }

        return $this->render('AppBundle:Entity/School/Binding:bounded.html.twig', [
            'objectClass' => $objectClass,
            'bounded'     => $bounded->getContent(),
            'school'      => $school
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/school/choose_for/{objectClass}/{objectId}",
     *      name="school_choose",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "objectClass" = "[a-z]+", "objectId" = "\d+"}
     * )
     */
    public function chooseAction($objectClass, $objectId)
    {
        if( !$this->_schoolBoundlessAccess->isGranted(SchoolBoundlessAccess::SCHOOL_BIND) )
            throw $this->createAccessDeniedException('Access denied');

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Employee, $objectClass):
                $employee = $object = $this->_manager->getRepository('AppBundle:Employee\Employee')->find($objectId);

                if( !$employee )
                    throw $this->createNotFoundException("Employee identified by `id` {$objectId} not found");

                if( !$this->isGranted(EmployeeVoter::EMPLOYEE_BIND_SCHOOL, $employee) )
                    throw $this->createAccessDeniedException('Access denied: Schools can be bound to manager only');

                $path = 'employee_update_bounded';

                $this->_breadcrumbs->add('employee_read')->add('employee_update', ['id' => $objectId])->add('employee_update_bounded',
                    [
                        'objectId'    => $objectId,
                        'objectClass' => 'school'
                    ],
                    $this->_translator->trans('school_read', [], 'routes')
                );
            break;

            case $this->compareObjectClassNameToString(new Settlement, $objectClass):
                $settlement = $object = $this->_manager->getRepository('AppBundle:Settlement\Settlement')->find($objectId);

                if( !$settlement )
                    throw $this->createNotFoundException("Settlement identified by `id` {$objectId} not found");

                $path = 'settlement_update_bounded';

                $this->_breadcrumbs->add('settlement_read')->add('settlement_update', ['id' => $objectId])->add('settlement_update_bounded',
                    [
                        'objectId'    => $objectId,
                        'objectClass' => 'school'
                    ],
                    $this->_translator->trans('school_read', [], 'routes')
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
            return $this->redirectToRoute('school_choose', $routeArguments);
        }

        $schools = $this->_entityResultsManager->findRecords(
            $this->_manager->getRepository('AppBundle:School\School')
        );

        if( $schools === FALSE )
            return $this->redirectToRoute('school_choose', $routeArguments);

        $schools = $this->filterDeletedIfNotGranted(
            SchoolVoter::SCHOOL_READ, $schools
        );

        $this->_breadcrumbs->add('school_choose', $routeArguments);

        return $this->render('AppBundle:Entity/School/Binding:choose.html.twig', [
            'path'    => $path,
            'schools' => $schools,
            'object'  => $object
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/school/bind/{targetId}/{objectClass}/{objectId}",
     *      name="school_bind",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "targetId" = "\d+", "objectClass" = "[a-z]+", "objectId" = "\d+"}
     * )
     */
    public function bindToAction(Request $request, $targetId, $objectClass, $objectId)
    {
        $school = $this->_manager->getRepository('AppBundle:School\School')->find($targetId);

        if( !$school )
            throw $this->createNotFoundException($this->_translator->trans('common.error.not_found', [], 'responses'));

        if( !$this->isGranted(SchoolVoter::SCHOOL_BIND, $school) )
            throw $this->createAccessDeniedException($this->_translator->trans('common.error.forbidden', [], 'responses'));

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Employee, $objectClass):
                $employee = $this->_manager->getRepository('AppBundle:Employee\Employee')->find($objectId);

                if( !$employee )
                    throw $this->createNotFoundException($this->_translator->trans('common.error.not_found', [], 'responses'));

                if( !$this->isGranted(EmployeeVoter::EMPLOYEE_BIND_SCHOOL, $employee) )
                    throw $this->createAccessDeniedException($this->_translator->trans('common.error.forbidden', [], 'responses'));

                $employee->addSchool($school);

                $this->_manager->persist($employee);
            break;

            case $this->compareObjectClassNameToString(new Settlement, $objectClass):
                $settlement = $this->_manager->getRepository('AppBundle:Settlement\Settlement')->find($objectId);

                if( !$settlement )
                    throw $this->createNotFoundException($this->_translator->trans('common.error.not_found', [], 'responses'));

                $settlement->addSchool($school);

                $this->_manager->persist($settlement);
            break;

            default:
                throw new NotAcceptableHttpException($this->_translator->trans('bind.error.not_boundalbe', [], 'responses'));
            break;
        }

        $this->_manager->flush();

        $this->_messages->markBindSuccess(
            $this->_translator->trans('bind.success.school', [], 'responses')
        );

        return new RedirectResponse($request->headers->get('referer'));
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/school/unbind/{targetId}/{objectClass}/{objectId}",
     *      name="school_unbind",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "targetId" = "\d+", "objectClass" = "[a-z]+", "objectId" = "\d+"}
     * )
     */
    public function unbindFromAction(Request $request, $targetId, $objectClass, $objectId)
    {
        $school = $this->_manager->getRepository('AppBundle:School\School')->find($targetId);

        if( !$school )
            throw $this->createNotFoundException($this->_translator->trans('common.error.not_found', [], 'responses'));

        if( !$this->isGranted(SchoolVoter::SCHOOL_BIND, $school) )
            throw $this->createAccessDeniedException($this->_translator->trans('common.error.forbidden', [], 'responses'));

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Employee, $objectClass):
                $employee = $this->_manager->getRepository('AppBundle:Employee\Employee')->find($objectId);

                if( !$employee )
                    throw $this->createNotFoundException($this->_translator->trans('common.error.not_found', [], 'responses'));

                $employee->removeSchool($school);

                $this->_manager->persist($employee);
            break;

            case $this->compareObjectClassNameToString(new Settlement, $objectClass):
                $school->setSettlement(NULL);
            break;

            default:
                throw new NotAcceptableHttpException($this->_translator->trans('bind.error.not_unboundalbe', [], 'responses'));
            break;
        }

        $this->_manager->flush();

        $this->_messages->markUnbindSuccess(
            $this->_translator->trans('unbind.success.school', [], 'responses')
        );

        return new RedirectResponse($request->headers->get('referer'));
    }
}
