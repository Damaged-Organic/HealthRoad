<?php
// AppBundle/Controller/Binding/SchoolController.php
namespace AppBundle\Controller\Binding;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\RedirectResponse,
    Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException,
    Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Controller\Utility\Traits\ClassOperationsTrait,
    AppBundle\Entity\Employee\Employee,
    AppBundle\Entity\Settlement\Settlement,
    AppBundle\Entity\VendingMachine\VendingMachine,
    AppBundle\Security\Authorization\Voter\EmployeeVoter,
    AppBundle\Security\Authorization\Voter\SchoolVoter,
    AppBundle\Security\Authorization\Voter\SettlementVoter,
    AppBundle\Service\Security\SchoolBoundlessAccess;

class SchoolController extends Controller implements UserRoleListInterface
{
    use ClassOperationsTrait;

    public function showAction($objectClass, $objectId)
    {
        $_schoolBoundlessAccess = $this->get('app.security.school_boundless_access');

        if( !$_schoolBoundlessAccess->isGranted(SchoolBoundlessAccess::SCHOOL_READ) )
            throw $this->createAccessDeniedException('Access denied');

        $_manager = $this->getDoctrine()->getManager();

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Employee, $objectClass):
                $object = $_manager->getRepository('AppBundle:Employee\Employee')->find($objectId);

                if( !$object )
                    throw $this->createNotFoundException("Employee identified by `id` {$objectId} not found");

                $schools = $object->getSchools();

                $action = [
                    'path'  => 'school_choose',
                    'voter' => EmployeeVoter::EMPLOYEE_BIND_SCHOOL
                ];
            break;

            case $this->compareObjectClassNameToString(new Settlement, $objectClass):
                $object = $_manager->getRepository('AppBundle:Settlement\Settlement')->find($objectId);

                if( !$object )
                    throw $this->createNotFoundException("Settlement identified by `id` {$objectId} not found");

                $schools = $_manager->getRepository('AppBundle:School\School')->findBy(['settlement' => $object]);

                $action = [
                    'path'  => 'school_choose',
                    'voter' => SettlementVoter::SETTLEMENT_BIND
                ];
            break;

            default:
                throw new NotAcceptableHttpException("Object not supported");
            break;
        }

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
        $_manager = $this->getDoctrine()->getManager();

        $_translator = $this->get('translator');

        $_breadcrumbs = $this->get('app.common.breadcrumbs');

        $school = $_manager->getRepository('AppBundle:School\School')->find($objectId);

        if( !$school )
            throw $this->createNotFoundException("School identified by `id` {$objectId} not found");

        if( !$this->isGranted(SchoolVoter::SCHOOL_READ, $school) )
            throw $this->createAccessDeniedException('Access denied');

        $_breadcrumbs->add('school_read')->add('school_update', ['id' => $objectId]);

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new VendingMachine, $objectClass):
                $bounded = $this->forward('AppBundle:Binding\VendingMachine:show', [
                    'objectClass' => $this->getObjectClassName($school),
                    'objectId'    => $objectId
                ]);

                $_breadcrumbs->add('school_update_bounded',
                    [
                        'objectId'    => $objectId,
                        'objectClass' => $objectClass
                    ],
                    $_translator->trans('vending_machine_read', [], 'routes')
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
        $_schoolBoundlessAccess = $this->get('app.security.school_boundless_access');

        if( !$_schoolBoundlessAccess->isGranted(SchoolBoundlessAccess::SCHOOL_BIND) )
            throw $this->createAccessDeniedException('Access denied');

        $_manager = $this->getDoctrine()->getManager();

        $_translator = $this->get('translator');

        $_breadcrumbs = $this->get('app.common.breadcrumbs');

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Employee, $objectClass):
                $employee = $object = $_manager->getRepository('AppBundle:Employee\Employee')->find($objectId);

                if( !$employee )
                    throw $this->createNotFoundException("Employee identified by `id` {$objectId} not found");

                if( !$this->isGranted(EmployeeVoter::EMPLOYEE_BIND_SCHOOL, $employee) )
                    throw $this->createAccessDeniedException('Access denied: Schools can be bound to manager only');

                $path = 'employee_update_bounded';

                $_breadcrumbs->add('employee_read')->add('employee_update', ['id' => $objectId])->add('employee_update_bounded',
                    [
                        'objectId'    => $objectId,
                        'objectClass' => 'school'
                    ],
                    $_translator->trans('school_read', [], 'routes')
                );
            break;

            case $this->compareObjectClassNameToString(new Settlement, $objectClass):
                $settlement = $object = $_manager->getRepository('AppBundle:Settlement\Settlement')->find($objectId);

                if( !$settlement )
                    throw $this->createNotFoundException("Settlement identified by `id` {$objectId} not found");

                $path = 'settlement_update_bounded';

                $_breadcrumbs->add('settlement_read')->add('settlement_update', ['id' => $objectId])->add('settlement_update_bounded',
                    [
                        'objectId'    => $objectId,
                        'objectClass' => 'school'
                    ],
                    $_translator->trans('school_read', [], 'routes')
                );
            break;

            default:
                throw new NotAcceptableHttpException("Object not supported");
            break;
        }

        $schools = $_manager->getRepository('AppBundle:School\School')->findAll();

        $_breadcrumbs->add('school_choose', [
            'objectId'    => $objectId,
            'objectClass' => $objectClass,
        ]);

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
        $_manager = $this->getDoctrine()->getManager();

        $_translator = $this->get('translator');

        $school = $_manager->getRepository('AppBundle:School\School')->find($targetId);

        if( !$school )
            throw $this->createNotFoundException($_translator->trans('common.error.not_found', [], 'responses'));

        if( !$this->isGranted(SchoolVoter::SCHOOL_BIND, $school) )
            throw $this->createAccessDeniedException($_translator->trans('common.error.forbidden', [], 'responses'));

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Employee, $objectClass):
                $employee = $_manager->getRepository('AppBundle:Employee\Employee')->find($objectId);

                if( !$employee )
                    throw $this->createNotFoundException($_translator->trans('common.error.not_found', [], 'responses'));

                if( !$this->isGranted(EmployeeVoter::EMPLOYEE_BIND_SCHOOL, $employee) )
                    throw $this->createAccessDeniedException($_translator->trans('common.error.forbidden', [], 'responses'));

                $employee->addSchool($school);

                $_manager->persist($employee);
            break;

            case $this->compareObjectClassNameToString(new Settlement, $objectClass):
                $settlement = $_manager->getRepository('AppBundle:Settlement\Settlement')->find($objectId);

                if( !$settlement )
                    throw $this->createNotFoundException($_translator->trans('common.error.not_found', [], 'responses'));

                $settlement->addSchool($school);

                $_manager->persist($settlement);
            break;

            default:
                throw new NotAcceptableHttpException($_translator->trans('bind.error.not_boundalbe', [], 'responses'));
            break;
        }

        $_manager->flush();

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
        $_manager = $this->getDoctrine()->getManager();

        $_translator = $this->get('translator');

        $school = $_manager->getRepository('AppBundle:School\School')->find($targetId);

        if( !$school )
            throw $this->createNotFoundException($_translator->trans('common.error.not_found', [], 'responses'));

        if( !$this->isGranted(SchoolVoter::SCHOOL_BIND, $school) )
            throw $this->createAccessDeniedException($_translator->trans('common.error.forbidden', [], 'responses'));

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Employee, $objectClass):
                $employee = $_manager->getRepository('AppBundle:Employee\Employee')->find($objectId);

                if( !$employee )
                    throw $this->createNotFoundException($_translator->trans('common.error.not_found', [], 'responses'));

                $employee->removeSchool($school);

                $_manager->persist($employee);
            break;

            case $this->compareObjectClassNameToString(new Settlement, $objectClass):
                $school->setSettlement(NULL);
            break;

            default:
                throw new NotAcceptableHttpException($_translator->trans('bind.error.not_unboundalbe', [], 'responses'));
            break;
        }

        $_manager->flush();

        return new RedirectResponse($request->headers->get('referer'));
    }
}