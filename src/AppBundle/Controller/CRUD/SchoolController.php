<?php
// AppBundle/Controller/CRUD/SchoolController.php
namespace AppBundle\Controller\CRUD;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request;

use AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Entity\School\School,
    AppBundle\Form\Type\SchoolType,
    AppBundle\Security\Authorization\Voter\SchoolVoter,
    AppBundle\Service\Security\SchoolBoundlessAccess;

class SchoolController extends Controller implements UserRoleListInterface
{
    /**
     * @Method({"GET"})
     * @Route(
     *      "/school/{id}",
     *      name="school_read",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = null},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function readAction($id = NULL)
    {
        $_manager = $this->getDoctrine()->getManager();

        $_schoolBoundlessAccess = $this->get('app.security.school_boundless_access');

        $_breadcrumbs = $this->get('app.common.breadcrumbs');

        if( $id )
        {
            $school = $_manager->getRepository('AppBundle:School\School')->find($id);

            if( !$school )
                throw $this->createNotFoundException("School identified by `id` {$id} not found");

            if( !$this->isGranted(SchoolVoter::SCHOOL_READ, $school) )
                throw $this->createAccessDeniedException('Access denied');

            $response = [
                'view' => 'AppBundle:Entity/School/CRUD:readItem.html.twig',
                'data' => ['school' => $school]
            ];
        } else {
            if( !$_schoolBoundlessAccess->isGranted(SchoolBoundlessAccess::SCHOOL_READ) )
                throw $this->createAccessDeniedException('Access denied');

            $schools = $_manager->getRepository('AppBundle:School\School')->findAll();

            $response = [
                'view' => 'AppBundle:Entity/School/CRUD:readList.html.twig',
                'data' => ['schools' => $schools]
            ];
        }

        $_breadcrumbs->add('school_read');

        return $this->render($response['view'], $response['data']);
    }

    /**
     * @Method({"GET", "POST"})
     * @Route(
     *      "/school/create",
     *      name="school_create",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"}
     * )
     */
    public function createAction(Request $request)
    {
        $_schoolBoundlessAccess = $this->get('app.security.school_boundless_access');

        if( !$_schoolBoundlessAccess->isGranted(SchoolBoundlessAccess::SCHOOL_CREATE) )
            throw $this->createAccessDeniedException('Access denied');

        $_breadcrumbs = $this->get('app.common.breadcrumbs');

        $schoolType = new SchoolType($_schoolBoundlessAccess->isGranted(SchoolBoundlessAccess::SCHOOL_CREATE));

        $form = $this->createForm($schoolType, $school = new School, [
            'action' => $this->generateUrl('school_create')
        ]);

        $form->handleRequest($request);

        if( !($form->isValid()) ) {
            $_breadcrumbs->add('school_read')->add('school_create');

            return $this->render('AppBundle:Entity/School/CRUD:createItem.html.twig', [
                'form' => $form->createView()
            ]);
        } else {
            $_manager = $this->getDoctrine()->getManager();

            $_manager->persist($school);
            $_manager->flush();

            if( $form->has('create_and_return') && $form->get('create_and_return')->isClicked() ) {
                return $this->redirectToRoute('school_read');
            } else {
                return $this->redirectToRoute('school_update', [
                    'id' => $school->getId()
                ]);
            }
        }
    }

    /**
     * @Method({"GET", "POST"})
     * @Route(
     *      "/school/update/{id}",
     *      name="school_update",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function updateAction(Request $request, $id)
    {
        $_manager = $this->getDoctrine()->getManager();

        $_schoolBoundlessAccess = $this->get('app.security.school_boundless_access');

        $_breadcrumbs = $this->get('app.common.breadcrumbs');

        $school = $_manager->getRepository('AppBundle:School\School')->find($id);

        if( !$school )
            throw $this->createNotFoundException("School identified by `id` {$id} not found");

        if( !$this->isGranted(SchoolVoter::SCHOOL_UPDATE, $school) ) {
            return $this->redirectToRoute('school_read', [
                'id' => $school->getId()
            ]);
        }

        $schoolType = new SchoolType($_schoolBoundlessAccess->isGranted(SchoolBoundlessAccess::SCHOOL_CREATE));

        $form = $this->createForm($schoolType, $school, [
            'action' => $this->generateUrl('school_update', ['id' => $id])
        ]);

        $form->handleRequest($request);

        if( $form->isValid() )
        {
            $_manager->flush();

            if( $form->has('update_and_return') && $form->get('update_and_return')->isClicked() ) {
                return $this->redirectToRoute('school_read');
            } else {
                return $this->redirectToRoute('school_update', [
                    'id' => $school->getId()
                ]);
            }
        }

        $_breadcrumbs->add('school_read')->add('school_update', ['id' => $id]);

        return $this->render('AppBundle:Entity/School/CRUD:updateItem.html.twig', [
            'form'   => $form->createView(),
            'school' => $school
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/school/delete/{id}",
     *      name="school_delete",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function deleteAction($id)
    {
        $_manager = $this->getDoctrine()->getManager();

        $school = $_manager->getRepository('AppBundle:School\School')->find($id);

        if( !$school )
            throw $this->createNotFoundException("School identified by `id` {$id} not found");

        if( !$this->isGranted(SchoolVoter::SCHOOL_DELETE, $school) )
            throw $this->createAccessDeniedException('Access denied');

        $_manager->remove($school);
        $_manager->flush();

        return $this->redirectToRoute('school_read');
    }
}