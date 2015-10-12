<?php
// AppBundle/Controller/CRUD/StudentController.php
namespace AppBundle\Controller\CRUD;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request;

use AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Entity\Student\Student,
    AppBundle\Form\Type\StudentType,
    AppBundle\Security\Authorization\Voter\StudentVoter,
    AppBundle\Service\Security\StudentBoundlessAccess;

class StudentController extends Controller implements UserRoleListInterface
{
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
        $_manager = $this->getDoctrine()->getManager();

        $_studentBoundlessAccess = $this->get('app.security.student_boundless_access');

        if( $id )
        {
            $student = $_manager->getRepository('AppBundle:Student\Student')->find($id);

            if( !$student )
                throw $this->createNotFoundException("Student identified by `id` {$id} not found");

            if( !$this->isGranted(StudentVoter::STUDENT_READ, $student) )
                throw $this->createAccessDeniedException('Access denied');

            $response = [
                'view' => 'AppBundle:Entity/Student/CRUD:readItem.html.twig',
                'data' => ['student' => $student]
            ];
        } else {
            if( !$_studentBoundlessAccess->isGranted(StudentBoundlessAccess::STUDENT_READ) )
                throw $this->createAccessDeniedException('Access denied');

            $students = $_manager->getRepository('AppBundle:Student\Student')->findAll();

            $response = [
                'view' => 'AppBundle:Entity/Student/CRUD:readList.html.twig',
                'data' => ['students' => $students]
            ];
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
        $_studentBoundlessAccess = $this->get('app.security.student_boundless_access');

        if( !$_studentBoundlessAccess->isGranted(StudentBoundlessAccess::STUDENT_CREATE) )
            throw $this->createAccessDeniedException('Access denied');

        $studentType = new StudentType($_studentBoundlessAccess->isGranted(StudentBoundlessAccess::STUDENT_CREATE));

        $form = $this->createForm($studentType, $student = new Student);

        $form->handleRequest($request);

        if( !($form->isValid()) ) {
            return $this->render('AppBundle:Entity/Student/CRUD:createItem.html.twig', [
                'form' => $form->createView()
            ]);
        } else {
            $_manager = $this->getDoctrine()->getManager();

            // Set employee who created student
            $student->setEmployee($this->getUser());

            $_manager->persist($student);
            $_manager->flush();

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
        $_manager = $this->getDoctrine()->getManager();

        $_studentBoundlessAccess = $this->get('app.security.student_boundless_access');

        $student = $_manager->getRepository('AppBundle:Student\Student')->find($id);

        if( !$student )
            throw $this->createNotFoundException("Student identified by `id` {$id} not found");

        if( !$this->isGranted(StudentVoter::STUDENT_UPDATE, $student) ) {
            return $this->redirectToRoute('student_read', [
                'id' => $student->getId()
            ]);
        }

        $studentType = new StudentType($_studentBoundlessAccess->isGranted(StudentBoundlessAccess::STUDENT_CREATE));

        $form = $this->createForm($studentType, $student);

        $form->handleRequest($request);

        if( $form->isValid() )
        {
            $_manager->flush();

            if( $form->has('update_and_return') && $form->get('update_and_return')->isClicked() ) {
                return $this->redirectToRoute('student_read');
            } else {
                return $this->redirectToRoute('student_update', [
                    'id' => $student->getId()
                ]);
            }
        }

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
        $_manager = $this->getDoctrine()->getManager();

        $student = $_manager->getRepository('AppBundle:Student\Student')->find($id);

        if( !$student )
            throw $this->createNotFoundException("Student identified by `id` {$id} not found");

        if( !$this->isGranted(StudentVoter::STUDENT_DELETE, $student) )
            throw $this->createAccessDeniedException('Access denied');

        $_manager->remove($student);
        $_manager->flush();

        return $this->redirectToRoute('student_read');
    }
}