<?php
// AppBundle/Controller/Binding/SchoolController.php
namespace AppBundle\Controller\Binding;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;

use AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Controller\Traits\ClassOperationsTrait,
    AppBundle\Entity\Employee\Employee,
    AppBundle\Entity\Settlement\Settlement,
    AppBundle\Service\Security\SchoolBoundlessAccess,
    AppBundle\Security\Authorization\Voter\EmployeeVoter,
    AppBundle\Security\Authorization\Voter\SchoolVoter;

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
                $employee = $_manager->getRepository('AppBundle:Employee\Employee')->find($objectId);

                if( !$employee )
                    throw $this->createNotFoundException("Employee identified by `id` {$objectId} not found");

                $schools = $employee->getSchools();
            break;

            case $this->compareObjectClassNameToString(new Settlement, $objectClass):
                $schools = $_manager->getRepository('AppBundle:School\School')->findBy(['settlement' => $objectId]);
            break;

            default:
                throw new NotAcceptableHttpException("Object not supported");
            break;
        }

        return $this->render('AppBundle:Entity/School/Binding:show.html.twig', [
            'schools'     => $schools,
            'objectId'    => $objectId,
            'objectClass' => $objectClass
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