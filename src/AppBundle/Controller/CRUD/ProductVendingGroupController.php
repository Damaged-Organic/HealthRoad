<?php
// AppBundle/Controller/CRUD/VendingMachineGroupController.php
namespace AppBundle\Controller\CRUD;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request;

use AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Entity\Product\ProductVendingGroup,
    AppBundle\Form\Type\ProductVendingGroupType,
    AppBundle\Security\Authorization\Voter\ProductVendingGroupVoter,
    AppBundle\Service\Security\ProductVendingGroupBoundlessAccess;

class ProductVendingGroupController extends Controller implements UserRoleListInterface
{
    /**
     * @Method({"GET"})
     * @Route(
     *      "/product_vending_group/{id}",
     *      name="product_vending_group_read",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = null},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function readAction($id = NULL)
    {
        $_manager = $this->getDoctrine()->getManager();

        $_productVendingGroupBoundlessAccess = $this->get('app.security.product_vending_group_boundless_access');

        if( $id )
        {
            $productVendingGroup = $_manager->getRepository('AppBundle:Product\ProductVendingGroup')->find($id);

            if( !$productVendingGroup )
                throw $this->createNotFoundException("Product Vending Group identified by `id` {$id} not found");

            if( !$this->isGranted(ProductVendingGroupVoter::PRODUCT_VENDING_GROUP_READ, $productVendingGroup) )
                throw $this->createAccessDeniedException('Access denied');

            $response = [
                'view' => 'AppBundle:Entity/ProductVendingGroup/CRUD:readItem.html.twig',
                'data' => ['productVendingGroup' => $productVendingGroup]
            ];
        } else {
            if( !$_productVendingGroupBoundlessAccess->isGranted(ProductVendingGroupBoundlessAccess::PRODUCT_VENDING_GROUP_READ) )
                throw $this->createAccessDeniedException('Access denied');

            $productVendingGroups = $_manager->getRepository('AppBundle:Product\ProductVendingGroup')->findAll();

            $response = [
                'view' => 'AppBundle:Entity/ProductVendingGroup/CRUD:readList.html.twig',
                'data' => ['productVendingGroups' => $productVendingGroups]
            ];
        }

        return $this->render($response['view'], $response['data']);
    }

    /**
     * @Method({"GET", "POST"})
     * @Route(
     *      "/product_vending_group/create",
     *      name="product_vending_group_create",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"}
     * )
     */
    public function createAction(Request $request)
    {
        $_productVendingGroupBoundlessAccess = $this->get('app.security.product_vending_group_boundless_access');

        if( !$_productVendingGroupBoundlessAccess->isGranted(ProductVendingGroupBoundlessAccess::PRODUCT_VENDING_GROUP_CREATE) )
            throw $this->createAccessDeniedException('Access denied');

        $productVendingGroupType = new ProductVendingGroupType($_productVendingGroupBoundlessAccess->isGranted(ProductVendingGroupBoundlessAccess::PRODUCT_VENDING_GROUP_CREATE));

        $form = $this->createForm($productVendingGroupType, $productVendingGroup = new ProductVendingGroup, [
            'action' => $this->generateUrl('product_vending_group_create')
        ]);

        $form->handleRequest($request);

        if( !($form->isValid()) ) {
            return $this->render('AppBundle:Entity/ProductVendingGroup/CRUD:createItem.html.twig', [
                'form' => $form->createView()
            ]);
        } else {
            $_manager = $this->getDoctrine()->getManager();

            $_manager->persist($productVendingGroup);
            $_manager->flush();

            if( $form->has('create_and_return') && $form->get('create_and_return')->isClicked() ) {
                return $this->redirectToRoute('product_vending_group_read');
            } else {
                return $this->redirectToRoute('product_vending_group_update', [
                    'id' => $productVendingGroup->getId()
                ]);
            }
        }
    }

    /**
     * @Method({"GET", "POST"})
     * @Route(
     *      "/product_vending_group/update/{id}",
     *      name="product_vending_group_update",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function updateAction(Request $request, $id)
    {
        $_manager = $this->getDoctrine()->getManager();

        $_productVendingGroupBoundlessAccess = $this->get('app.security.product_vending_group_boundless_access');

        $productVendingGroup = $_manager->getRepository('AppBundle:Product\ProductVendingGroup')->find($id);

        if( !$productVendingGroup )
            throw $this->createNotFoundException("Product Vending Group identified by `id` {$id} not found");

        if( !$this->isGranted(ProductVendingGroupVoter::PRODUCT_VENDING_GROUP_UPDATE, $productVendingGroup) ) {
            return $this->redirectToRoute('product_vending_group_read', [
                'id' => $productVendingGroup->getId()
            ]);
        }

        $productVendingGroupType = new ProductVendingGroupType($_productVendingGroupBoundlessAccess->isGranted(ProductVendingGroupBoundlessAccess::PRODUCT_VENDING_GROUP_CREATE));

        $form = $this->createForm($productVendingGroupType, $productVendingGroup, [
            'action' => $this->generateUrl('product_vending_group_update', ['id' => $id])
        ]);

        $form->handleRequest($request);

        if( $form->isValid() )
        {
            $_manager->flush();

            if( $form->has('update_and_return') && $form->get('update_and_return')->isClicked() ) {
                return $this->redirectToRoute('product_vending_group_read');
            } else {
                return $this->redirectToRoute('product_vending_group_update', [
                    'id' => $productVendingGroup->getId()
                ]);
            }
        }

        return $this->render('AppBundle:Entity/ProductVendingGroup/CRUD:updateItem.html.twig', [
            'form'                => $form->createView(),
            'productVendingGroup' => $productVendingGroup
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/product_vending_group/delete/{id}",
     *      name="product_vending_group_delete",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function deleteAction($id)
    {
        $_manager = $this->getDoctrine()->getManager();

        $productVendingGroup = $_manager->getRepository('AppBundle:Product\ProductVendingGroup')->find($id);

        if( !$productVendingGroup )
            throw $this->createNotFoundException("Product Vending Group identified by `id` {$id} not found");

        if( !$this->isGranted(ProductVendingGroupVoter::PRODUCT_VENDING_GROUP_DELETE, $productVendingGroup) )
            throw $this->createAccessDeniedException('Access denied');

        $_manager->remove($productVendingGroup);
        $_manager->flush();

        return $this->redirectToRoute('product_vending_group_read');
    }
}