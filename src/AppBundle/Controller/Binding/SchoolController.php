<?php
// AppBundle/Controller/Binding/SchoolController.php
namespace AppBundle\Controller\Binding;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;

use AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Controller\Utility\Traits\ClassOperationsTrait,
    AppBundle\Security\Authorization\Voter\EmployeeVoter,
    AppBundle\Security\Authorization\Voter\SchoolVoter,
    AppBundle\Security\Authorization\Voter\SettlementVoter,
    AppBundle\Service\Security\SchoolBoundlessAccess,
    AppBundle\Entity\Employee\Employee,
    AppBundle\Entity\Settlement\Settlement;

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

        $_breadcrumbs = $this->get('app.common.breadcrumbs');

        $_translator = $this->get('translator');

        $school = $_manager->getRepository('AppBundle:School\School')->find($objectId);

        if( !$school )
            throw $this->createNotFoundException("School identified by `id` {$objectId} not found");

        if( !$this->isGranted(SchoolVoter::SCHOOL_READ, $school) )
            throw $this->createAccessDeniedException('Access denied');

        $_breadcrumbs->add('school_read')->add('school_update', ['id' => $objectId]);

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Settlement, $objectClass):
                $bounded = $this->forward('AppBundle:Binding\Settlement:show', [
                    'objectClass' => $this->getObjectClassName($school),
                    'objectId'    => $objectId
                ]);

                $_breadcrumbs->add('school_update_bounded',
                    [
                        'objectId'    => $objectId,
                        'objectClass' => $objectClass
                    ],
                    $_translator->trans('settlement_read', [], 'routes')
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

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Employee, $objectClass):
                $employee = $_manager->getRepository('AppBundle:Employee\Employee')->find($objectId);

                if( !$employee )
                    throw $this->createNotFoundException("Employee identified by `id` {$objectId} not found");

                if( !$this->isGranted(EmployeeVoter::EMPLOYEE_BIND_SCHOOL, $employee) )
                    throw $this->createAccessDeniedException('Access denied: Schools can be bound to manager only');

                $object = [
                    'class' => $this->getObjectClassName($employee),
                    'id'    => $employee->getId()
                ];
            break;

            case $this->compareObjectClassNameToString(new Settlement, $objectClass):
                $settlement = $_manager->getRepository('AppBundle:Settlement\Settlement')->find($objectId);

                if( !$settlement )
                    throw $this->createNotFoundException("Settlement identified by `id` {$objectId} not found");

                $object = [
                    'class' => $this->getObjectClassName($settlement),
                    'id'    => $settlement->getId()
                ];
            break;

            default:
                throw new NotAcceptableHttpException("Object not supported");
            break;
        }

        $schools = $_manager->getRepository('AppBundle:School\School')->findAll();

        return $this->render('AppBundle:Entity/School/Binding:choose.html.twig', [
            'schools'     => $schools,
            'objectClass' => $object['class'],
            'objectId'    => $object['id']
        ]);
    }

    /**
     * @Method({"POST"})
     * @Route(
     *      "/school/bind",
     *      name="school_bind",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"}
     * )
     */
    public function bindToAction(Request $request)
    {
        $schoolId = ( $request->request->has('schoolId') ) ? $request->request->get('schoolId') : NULL;

        $_manager = $this->getDoctrine()->getManager();

        $school = $_manager->getRepository('AppBundle:School\School')->find($schoolId);

        if( !$school )
            throw $this->createNotFoundException("School identified by `id` {$schoolId} not found");

        if( !$this->isGranted(SchoolVoter::SCHOOL_BIND, $school) )
            throw $this->createAccessDeniedException('Access denied');

        $objectClass = ( $request->request->get('objectClass') ) ? $request->request->get('objectClass') : NULL;
        $objectId    = ( $request->request->get('objectId') ) ? $request->request->get('objectId') : NULL;

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Employee, $objectClass):
                $employee = $_manager->getRepository('AppBundle:Employee\Employee')->find($objectId);

                if( !$employee )
                    throw $this->createNotFoundException("Employee identified by `id` {$objectId} not found");

                if( !$this->isGranted(EmployeeVoter::EMPLOYEE_BIND_SCHOOL, $employee) )
                    throw $this->createAccessDeniedException('Access denied: Regions can be bound to manager only');

                $employee->addSchool($school);

                $_manager->persist($employee);

                $redirect = [
                    'route' => "employee_update",
                    'id'    => $employee->getId()
                ];
            break;

            case $this->compareObjectClassNameToString(new Settlement, $objectClass):
                $settlement = $_manager->getRepository('AppBundle:Settlement\Settlement')->find($objectId);

                if( !$settlement )
                    throw $this->createNotFoundException("Settlement identified by `id` {$objectId} not found");

                $settlement->addSchool($school);

                $_manager->persist($settlement);

                $redirect = [
                    'route' => "settlement_update",
                    'id'    => $settlement->getId()
                ];
            break;

            default:
                throw $this->createNotFoundException("Object not supported");
            break;
        }

        $_manager->flush();

        return $this->redirectToRoute($redirect['route'], [
            'id' => $redirect['id']
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/school/unbind/{id}/{objectClass}/{objectId}",
     *      name="school_unbind",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+", "objectClass" = "[a-z]+", "objectId" = "\d+"}
     * )
     */
    public function unbindFromAction($id, $objectClass, $objectId)
    {
        $_manager = $this->getDoctrine()->getManager();

        $school = $_manager->getRepository('AppBundle:School\School')->find($id);

        if( !$school )
            throw $this->createNotFoundException("School identified by `id` {$id} not found");

        if( !$this->isGranted(SchoolVoter::SCHOOL_BIND, $school) )
            throw $this->createAccessDeniedException('Access denied');

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Employee, $objectClass):
                $employee = $_manager->getRepository('AppBundle:Employee\Employee')->find($objectId);

                if( !$employee )
                    throw $this->createNotFoundException("Employee identified by `id` {$objectId} not found");

                $employee->removeSchool($school);

                $_manager->persist($employee);

                $redirect = [
                    'route' => "employee_update",
                    'id'    => $employee->getId()
                ];
            break;

            case $this->compareObjectClassNameToString(new Settlement, $objectClass):
                //this should be gone in AJAX version
                $settlementId = $school->getSettlement()->getId();

                $school->setSettlement(NULL);

                $redirect = [
                    'route' => "settlement_update",
                    'id'    => $settlementId
                ];
            break;

            default:
                throw $this->createNotFoundException("Object not supported");
            break;
        }

        $_manager->flush();

        return $this->redirectToRoute($redirect['route'], [
            'id' => $redirect['id']
        ]);
    }
}