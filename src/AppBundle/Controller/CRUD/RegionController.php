<?php
// AppBundle/Controller/CRUD/RegionController.php
namespace AppBundle\Controller\CRUD;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request;

use AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Entity\Region\Region,
    AppBundle\Form\Type\RegionType,
    AppBundle\Security\Authorization\Voter\RegionVoter,
    AppBundle\Service\Security\RegionBoundlessAccess;

class RegionController extends Controller implements UserRoleListInterface
{
    /**
     * @Method({"GET"})
     * @Route(
     *      "/region/{id}",
     *      name="region_read",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = null},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function readAction($id = NULL)
    {
        $_manager = $this->getDoctrine()->getManager();

        $_regionBoundlessAccess = $this->get('app.security.region_boundless_access');

        $_breadcrumbs = $this->get('app.common.breadcrumbs');

        if( $id )
        {
            $region = $_manager->getRepository('AppBundle:Region\Region')->find($id);

            if( !$region )
                throw $this->createNotFoundException("Region identified by `id` {$id} not found");

            if( !$this->isGranted(RegionVoter::REGION_READ, $region) )
                throw $this->createAccessDeniedException('Access denied');

            $response = [
                'view' => 'AppBundle:Entity/Region/CRUD:readItem.html.twig',
                'data' => ['region' => $region]
            ];
        } else {
            if( !$_regionBoundlessAccess->isGranted(RegionBoundlessAccess::REGION_READ) )
                throw $this->createAccessDeniedException('Access denied');

            $regions = $_manager->getRepository('AppBundle:Region\Region')->findAll();

            $response = [
                'view' => 'AppBundle:Entity/Region/CRUD:readList.html.twig',
                'data' => ['regions' => $regions]
            ];
        }

        $_breadcrumbs->add('region_read');

        return $this->render($response['view'], $response['data']);
    }

    /**
     * @Method({"GET", "POST"})
     * @Route(
     *      "/region/create",
     *      name="region_create",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"}
     * )
     */
    public function createAction(Request $request)
    {
        $_regionBoundlessAccess = $this->get('app.security.region_boundless_access');

        $_breadcrumbs = $this->get('app.common.breadcrumbs');

        if( !$_regionBoundlessAccess->isGranted(RegionBoundlessAccess::REGION_CREATE) )
            throw $this->createAccessDeniedException('Access denied');

        $regionType = new RegionType($_regionBoundlessAccess->isGranted(RegionBoundlessAccess::REGION_CREATE));

        $form = $this->createForm($regionType, $region = new Region);

        $form->handleRequest($request);

        if( !($form->isValid()) ) {
            $_breadcrumbs->add('region_read')->add('region_create');

            return $this->render('AppBundle:Entity/Region/CRUD:createItem.html.twig', [
                'form' => $form->createView()
            ]);
        } else {
            $_manager = $this->getDoctrine()->getManager();

            $_manager->persist($region);
            $_manager->flush();

            if( $form->has('create_and_return') && $form->get('create_and_return')->isClicked() ) {
                return $this->redirectToRoute('region_read');
            } else {
                return $this->redirectToRoute('region_update', [
                    'id' => $region->getId()
                ]);
            }
        }
    }

    /**
     * @Method({"GET", "POST"})
     * @Route(
     *      "/region/update/{id}",
     *      name="region_update",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function updateAction(Request $request, $id)
    {
        $_manager = $this->getDoctrine()->getManager();

        $_regionBoundlessAccess = $this->get('app.security.region_boundless_access');

        $_breadcrumbs = $this->get('app.common.breadcrumbs');

        $region = $_manager->getRepository('AppBundle:Region\Region')->find($id);

        if( !$region )
            throw $this->createNotFoundException("Region identified by `id` {$id} not found");

        if( !$this->isGranted(RegionVoter::REGION_UPDATE, $region) ) {
            return $this->redirectToRoute('region_read', [
                'id' => $region->getId()
            ]);
        }

        $regionType = new RegionType($_regionBoundlessAccess->isGranted(RegionBoundlessAccess::REGION_CREATE));

        $form = $this->createForm($regionType, $region);

        $form->handleRequest($request);

        if( $form->isValid() )
        {
            $_manager->flush();

            if( $form->has('update_and_return') && $form->get('update_and_return')->isClicked() ) {
                return $this->redirectToRoute('region_read');
            } else {
                return $this->redirectToRoute('region_update', [
                    'id' => $region->getId()
                ]);
            }
        }

        $_breadcrumbs->add('region_read')->add('region_update', ['id' => $id]);

        return $this->render('AppBundle:Entity/Region/CRUD:updateItem.html.twig', [
            'form'   => $form->createView(),
            'region' => $region
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/region/delete/{id}",
     *      name="region_delete",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function deleteAction($id)
    {
        $_manager = $this->getDoctrine()->getManager();

        $region = $_manager->getRepository('AppBundle:Region\Region')->find($id);

        if( !$region )
            throw $this->createNotFoundException("Region identified by `id` {$id} not found");

        if( !$this->isGranted(RegionVoter::REGION_DELETE, $region) )
            throw $this->createAccessDeniedException('Access denied');

        $_manager->remove($region);
        $_manager->flush();

        return $this->redirectToRoute('region_read');
    }
}