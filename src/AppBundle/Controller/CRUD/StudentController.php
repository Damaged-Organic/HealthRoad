<?php
// AppBundle/Controller/CRUD/StudentController.php
namespace AppBundle\Controller\CRUD;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request;

use JMS\DiExtraBundle\Annotation as DI;

use AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Entity\Student\Student,
    AppBundle\Form\Type\StudentType,
    AppBundle\Security\Authorization\Voter\StudentVoter,
    AppBundle\Service\Security\StudentBoundlessAccess;

class StudentController extends Controller implements UserRoleListInterface
{
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

    /**
     * @Method({"GET"})
     * @Route(
     *      "/student/{id}",
     *      name="student_read",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = null},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function readAction($id = NULL)
    {
        if( $id )
        {
            $student = $this->_manager->getRepository('AppBundle:Student\Student')->find($id);

            if( !$student )
                throw $this->createNotFoundException("Student identified by `id` {$id} not found");

            if( !$this->isGranted(StudentVoter::STUDENT_READ, $student) )
                throw $this->createAccessDeniedException('Access denied');

            $response = [
                'view' => 'AppBundle:Entity/Student/CRUD:readItem.html.twig',
                'data' => ['student' => $student]
            ];

            $this->_breadcrumbs->add('student_read')->add('student_read', ['id' => $id], $this->_translator->trans('student_view', [], 'routes'));
        } else {
            if( !$this->_studentBoundlessAccess->isGranted(StudentBoundlessAccess::STUDENT_READ) )
                throw $this->createAccessDeniedException('Access denied');

            $students = $this->_manager->getRepository('AppBundle:Student\Student')->findAll();

            $response = [
                'view' => 'AppBundle:Entity/Student/CRUD:readList.html.twig',
                'data' => ['students' => $students]
            ];

            $this->_breadcrumbs->add('student_read');
        }

        return $this->render($response['view'], $response['data']);
    }

    /**
     * @Method({"GET", "POST"})
     * @Route(
     *      "/student/create",
     *      name="student_create",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"}
     * )
     */
    public function createAction(Request $request)
    {
        if( !$this->_studentBoundlessAccess->isGranted(StudentBoundlessAccess::STUDENT_CREATE) )
            throw $this->createAccessDeniedException('Access denied');

        $studentType = new StudentType(
            $this->_translator,
            $this->_studentBoundlessAccess->isGranted(StudentBoundlessAccess::STUDENT_CREATE)
        );

        $form = $this->createForm($studentType, $student = new Student, [
            'action' => $this->generateUrl('student_create')
        ]);

        $form->handleRequest($request);

        if( !($form->isValid()) ) {
            $this->_breadcrumbs->add('student_read')->add('student_create');

            return $this->render('AppBundle:Entity/Student/CRUD:createItem.html.twig', [
                'form' => $form->createView()
            ]);
        } else {
            // Set employee who created student
            $student->setEmployee($this->getUser());

            $this->_manager->persist($student);
            $this->_manager->flush();

            $this->_messages->markCreateSuccess();

            if( $form->has('create_and_return') && $form->get('create_and_return')->isClicked() ) {
                return $this->redirectToRoute('student_read');
            } else {
                return $this->redirectToRoute('student_update', [
                    'id' => $student->getId()
                ]);
            }
        }
    }

    /**
     * @Method({"GET", "POST"})
     * @Route(
     *      "/student/update/{id}",
     *      name="student_update",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function updateAction(Request $request, $id)
    {
        $student = $this->_manager->getRepository('AppBundle:Student\Student')->find($id);

        if( !$student )
            throw $this->createNotFoundException("Student identified by `id` {$id} not found");

        if( !$this->isGranted(StudentVoter::STUDENT_UPDATE, $student) ) {
            return $this->redirectToRoute('student_read', [
                'id' => $student->getId()
            ]);
        }

        $studentType = new StudentType(
            $this->_translator,
            $this->_studentBoundlessAccess->isGranted(StudentBoundlessAccess::STUDENT_CREATE)
        );

        $form = $this->createForm($studentType, $student, [
            'action' => $this->generateUrl('student_update', ['id' => $id])
        ]);

        $form->handleRequest($request);

        if( $form->isValid() )
        {
            $this->_manager->flush();

            $this->_messages->markUpdateSuccess();

            if( $form->has('update_and_return') && $form->get('update_and_return')->isClicked() ) {
                return $this->redirectToRoute('student_read');
            } else {
                return $this->redirectToRoute('student_update', [
                    'id' => $student->getId()
                ]);
            }
        }

        $this->_breadcrumbs->add('student_read')->add('student_update', ['id' => $id]);

        return $this->render('AppBundle:Entity/Student/CRUD:updateItem.html.twig', [
            'form'    => $form->createView(),
            'student' => $student
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/student/delete/{id}",
     *      name="student_delete",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function deleteAction($id)
    {
        $student = $this->_manager->getRepository('AppBundle:Student\Student')->find($id);

        if( !$student )
            throw $this->createNotFoundException("Student identified by `id` {$id} not found");

        if( !$this->isGranted(StudentVoter::STUDENT_DELETE, $student) )
            throw $this->createAccessDeniedException('Access denied');

        if( !$student->getPseudoDeleted() )
        {
            $student->setPseudoDeleted(TRUE);

            $this->_manager->flush();

            $this->_messages->markDeleteSuccess();
        } else {
            $student->setPseudoDeleted(FALSE);

            $this->_manager->flush();

            $this->_messages->markUnDeleteSuccess();
        }

        return $this->redirectToRoute('student_read');
    }
}