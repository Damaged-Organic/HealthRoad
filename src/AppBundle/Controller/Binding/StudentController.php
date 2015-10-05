<?php
// AppBundle/Controller/Binding/StudentController.php
namespace AppBundle\Controller\Binding;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;

use AppBundle\Controller\Traits\ClassOperationsTrait,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Service\Security\StudentBoundlessAccess,
    AppBundle\Security\Authorization\Voter\StudentVoter,
    AppBundle\Entity\Customer\Customer;

class StudentController extends Controller implements UserRoleListInterface
{
    use ClassOperationsTrait;

    public function showAction($objectClass, $objectId)
    {
        $_studentBoundlessAccess = $this->get('app.security.student_boundless_access');

        if( !$_studentBoundlessAccess->isGranted(StudentBoundlessAccess::STUDENT_READ) )
            throw $this->createAccessDeniedException('Access denied');

        $_manager = $this->getDoctrine()->getManager();

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Customer, $objectClass):
                $customer = $_manager->getRepository('AppBundle:Customer\Customer')->find($objectId);

                if( !$customer )
                    throw $this->createNotFoundException("Customer identified by `id` {$objectId} not found");

                $students = $customer->getStudents();
            break;

            default:
                throw new NotAcceptableHttpException("Object not supported");
            break;
        }

        return $this->render('AppBundle:Entity/Student/Binding:show.html.twig', [
            'students'    => $students,
            'objectId'    => $objectId,
            'objectClass' => $objectClass
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/student/choose_for/{objectClass}/{objectId}",
     *      name="student_choose",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "objectClass" = "[a-z]+", "objectId" = "\d+"}
     * )
     */
    public function chooseAction($objectClass, $objectId)
    {
        $_studentBoundlessAccess = $this->get('app.security.student_boundless_access');

        if( !$_studentBoundlessAccess->isGranted(StudentBoundlessAccess::STUDENT_BIND) )
            throw $this->createAccessDeniedException('Access denied');

        $_manager = $this->getDoctrine()->getManager();

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Customer, $objectClass):
                $customer = $_manager->getRepository('AppBundle:Customer\Customer')->find($objectId);

                if( !$customer )
                    throw $this->createNotFoundException("Customer identified by `id` {$objectId} not found");

                $object = [
                    'class' => $this->getObjectClassName($customer),
                    'id'    => $customer->getId()
                ];
            break;

            default:
                throw new NotAcceptableHttpException("Object not supported");
            break;
        }

        $students = $_manager->getRepository('AppBundle:Student\Student')->findAll();

        return $this->render('AppBundle:Entity/Student/Binding:choose.html.twig', [
            'students'    => $students,
            'objectClass' => $object['class'],
            'objectId'    => $object['id']
        ]);
    }

    /**
     * @Method({"POST"})
     * @Route(
     *      "/student/bind",
     *      name="student_bind",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"}
     * )
     */
    public function bindToAction(Request $request)
    {
        $studentId = ( $request->request->has('studentId') ) ? $request->request->get('studentId') : NULL;

        $_manager = $this->getDoctrine()->getManager();

        $student = $_manager->getRepository('AppBundle:Student\Student')->find($studentId);

        if( !$student )
            throw $this->createNotFoundException("Student identified by `id` {$studentId} not found");

        if( !$this->isGranted(StudentVoter::STUDENT_BIND, $student) )
            throw $this->createAccessDeniedException('Access denied');

        $objectClass = ( $request->request->get('objectClass') ) ? $request->request->get('objectClass') : NULL;
        $objectId    = ( $request->request->get('objectId') ) ? $request->request->get('objectId') : NULL;

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Customer, $objectClass):
                $customer = $_manager->getRepository('AppBundle:Customer\Customer')->find($objectId);

                if( !$customer )
                    throw $this->createNotFoundException("Customer identified by `id` {$objectId} not found");

                $customer->addStudent($student);

                $_manager->persist($customer);

                $redirect = [
                    'route' => "customer_update",
                    'id'    => $customer->getId()
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
     *      "/student/unbind/{id}/{objectClass}/{objectId}",
     *      name="student_unbind",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+", "objectClass" = "[a-z]+", "objectId" = "\d+"}
     * )
     */
    public function unbindFromAction($id, $objectClass, $objectId)
    {
        $_manager = $this->getDoctrine()->getManager();

        $student = $_manager->getRepository('AppBundle:Student\Student')->find($id);

        if( !$student )
            throw $this->createNotFoundException("Student identified by `id` {$id} not found");

        if( !$this->isGranted(StudentVoter::STUDENT_BIND, $student) )
            throw $this->createAccessDeniedException('Access denied');

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Customer, $objectClass):
                /*$customer = $_manager->getRepository('AppBundle:Customer\Customer')->find($objectId);

                if( !$customer )
                    throw $this->createNotFoundException("Customer identified by `id` {$objectId} not found");

                $customer->removeStudent($student);

                $_manager->persist($customer);

                $redirect = [
                    'route' => "customer_update",
                    'id'    => $customer->getId()
                ];*/

                //this should be gone in AJAX version
                $customerId = $student->getCustomer()->getId();

                $student->setCustomer(NULL);

                $redirect = [
                    'route' => "customer_update",
                    'id'    => $customerId
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