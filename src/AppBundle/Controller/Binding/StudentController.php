<?php
// AppBundle/Controller/Binding/StudentController.php
namespace AppBundle\Controller\Binding;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\RedirectResponse,
    Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;

use JMS\DiExtraBundle\Annotation as DI;

use AppBundle\Controller\Utility\Traits\ClassOperationsTrait,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Entity\Customer\Customer,
    AppBundle\Entity\NfcTag\NfcTag,
    AppBundle\Entity\Product\Product,
    AppBundle\Security\Authorization\Voter\StudentVoter,
    AppBundle\Security\Authorization\Voter\CustomerVoter,
    AppBundle\Service\Security\StudentBoundlessAccess;

class StudentController extends Controller implements UserRoleListInterface
{
    use ClassOperationsTrait;

    /** @DI\Inject("doctrine.orm.entity_manager") */
    private $_manager;

    /** @DI\Inject("translator") */
    private $_translator;

    /** @DI\Inject("app.common.breadcrumbs") */
    private $_breadcrumbs;

    /** @DI\Inject("app.common.messages") */
    private $_messages;

    /** @DI\Inject("app.security.student_boundless_access") */
    private $_studentBoundlessAccess;

    public function showAction($objectClass, $objectId)
    {
        if( !$this->_studentBoundlessAccess->isGranted(StudentBoundlessAccess::STUDENT_READ) )
            throw $this->createAccessDeniedException('Access denied');

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Customer, $objectClass):
                $object = $this->_manager->getRepository('AppBundle:Customer\Customer')->find($objectId);

                if( !$object )
                    throw $this->createNotFoundException("Customer identified by `id` {$objectId} not found");

                $students = $object->getStudents();

                $action = [
                    'path'  => 'student_choose',
                    'voter' => CustomerVoter::CUSTOMER_BIND
                ];
            break;

            default:
                throw new NotAcceptableHttpException("Object not supported");
            break;
        }

        return $this->render('AppBundle:Entity/Student/Binding:show.html.twig', [
            'standalone' => TRUE,
            'students'   => $students,
            'object'     => $object,
            'action'     => $action
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/student/update/{objectId}/bounded/{objectClass}",
     *      name="student_update_bounded",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "objectId" = "\d+", "objectClass" = "[a-z]+"}
     * )
     */
    public function boundedAction($objectId, $objectClass)
    {
        $student = $this->_manager->getRepository('AppBundle:Student\Student')->find($objectId);

        if( !$student )
            throw $this->createNotFoundException("Student identified by `id` {$objectId} not found");

        if( !$this->isGranted(StudentVoter::STUDENT_READ, $student) )
            throw $this->createAccessDeniedException('Access denied');

        $this->_breadcrumbs->add('student_read')->add('student_update', ['id' => $objectId], $this->_translator->trans('student_bounded', [], 'routes'));

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new NfcTag, $objectClass):
                $bounded = $this->forward('AppBundle:Binding\NfcTag:show', [
                    'objectClass' => $this->getObjectClassName($student),
                    'objectId'    => $objectId
                ]);

                $this->_breadcrumbs->add('student_update_bounded',
                    [
                        'objectId'    => $objectId,
                        'objectClass' => $objectClass
                    ],
                    $this->_translator->trans('nfc_tag_read', [], 'routes')
                );
            break;

            case $this->compareObjectClassNameToString(new Product, $objectClass):
                $bounded = $this->forward('AppBundle:Binding\Product:show', [
                    'objectClass' => $this->getObjectClassName($student),
                    'objectId'    => $objectId
                ]);

                $this->_breadcrumbs->add('student_update_bounded',
                    [
                        'objectId'    => $objectId,
                        'objectClass' => $objectClass
                    ],
                    $this->_translator->trans('product_read_restricted', [], 'routes')
                );
            break;

            default:
                throw new NotAcceptableHttpException("Object not supported");
            break;
        }

        return $this->render('AppBundle:Entity/Student/Binding:bounded.html.twig', [
            'objectClass' => $objectClass,
            'bounded'     => $bounded->getContent(),
            'student'     => $student
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
        if( !$this->_studentBoundlessAccess->isGranted(StudentBoundlessAccess::STUDENT_BIND) )
            throw $this->createAccessDeniedException('Access denied');

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Customer, $objectClass):
                $customer = $object = $this->_manager->getRepository('AppBundle:Customer\Customer')->find($objectId);

                if( !$customer )
                    throw $this->createNotFoundException("Customer identified by `id` {$objectId} not found");

                $path = 'customer_update_bounded';

                $this->_breadcrumbs->add('customer_read')->add('customer_update', ['id' => $objectId])->add('customer_update_bounded',
                    [
                        'objectId'    => $objectId,
                        'objectClass' => 'region'
                    ],
                    $this->_translator->trans('student_read', [], 'routes')
                );
            break;

            default:
                throw new NotAcceptableHttpException("Object not supported");
            break;
        }

        $students = $this->_manager->getRepository('AppBundle:Student\Student')->findAll();

        $this->_breadcrumbs->add('student_choose', [
            'objectId'    => $objectId,
            'objectClass' => $objectClass
        ]);

        return $this->render('AppBundle:Entity/Student/Binding:choose.html.twig', [
            'path'     => $path,
            'students' => $students,
            'object'   => $object
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/student/bind/{targetId}/{objectClass}/{objectId}",
     *      name="student_bind",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "targetId" = "\d+", "objectClass" = "[a-z]+", "objectId" = "\d+"}
     * )
     */
    public function bindToAction(Request $request, $targetId, $objectClass, $objectId)
    {
        $student = $this->_manager->getRepository('AppBundle:Student\Student')->find($targetId);

        if( !$student )
            throw $this->createNotFoundException($this->_translator->trans('common.error.not_found', [], 'responses'));

        if( !$this->isGranted(StudentVoter::STUDENT_BIND, $student) )
            throw $this->createAccessDeniedException($this->_translator->trans('common.error.forbidden', [], 'responses'));

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Customer, $objectClass):
                $customer = $this->_manager->getRepository('AppBundle:Customer\Customer')->find($objectId);

                if( !$customer )
                    throw $this->createNotFoundException($this->_translator->trans('common.error.not_found', [], 'responses'));

                $customer->addStudent($student);

                $this->_manager->persist($customer);
            break;

            default:
                throw new NotAcceptableHttpException($this->_translator->trans('bind.error.not_boundalbe', [], 'responses'));
            break;
        }

        $this->_manager->flush();

        $this->_messages->markBindSuccess(
            $this->_translator->trans('bind.success.student', [], 'responses')
        );

        return new RedirectResponse($request->headers->get('referer'));
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/student/unbind/{targetId}/{objectClass}/{objectId}",
     *      name="student_unbind",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "targetId" = "\d+", "objectClass" = "[a-z]+", "objectId" = "\d+"}
     * )
     */
    public function unbindFromAction(Request $request, $targetId, $objectClass, $objectId)
    {
        $student = $this->_manager->getRepository('AppBundle:Student\Student')->find($targetId);

        if( !$student )
            throw $this->createNotFoundException($this->_translator->trans('common.error.not_found', [], 'responses'));

        if( !$this->isGranted(StudentVoter::STUDENT_BIND, $student) )
            throw $this->createAccessDeniedException($this->_translator->trans('common.error.forbidden', [], 'responses'));

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Customer, $objectClass):
                $student->setCustomer(NULL);
            break;

            default:
                throw new NotAcceptableHttpException($this->_translator->trans('bind.error.not_unboundalbe', [], 'responses'));
            break;
        }

        $this->_manager->flush();

        $this->_messages->markUnbindSuccess(
            $this->_translator->trans('unbind.success.student', [], 'responses')
        );

        return new RedirectResponse($request->headers->get('referer'));
    }
}