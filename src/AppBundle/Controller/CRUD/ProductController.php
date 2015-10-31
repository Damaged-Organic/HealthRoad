<?php
// AppBundle/Controller/CRUD/ProductController.php
namespace AppBundle\Controller\CRUD;

use DateTime;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Entity\Product\Product,
    AppBundle\Entity\Product\ProductImage,
    AppBundle\Form\Type\ProductType,
    AppBundle\Security\Authorization\Voter\ProductVoter,
    AppBundle\Service\Security\ProductBoundlessAccess;

class ProductController extends Controller implements UserRoleListInterface
{
    /**
     * @Method({"GET"})
     * @Route(
     *      "/product/{id}",
     *      name="product_read",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = null},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function readAction($id = NULL)
    {
        $_manager = $this->getDoctrine()->getManager();

        $_productBoundlessAccess = $this->get('app.security.product_boundless_access');

        $_translator = $this->get('translator');

        $_breadcrumbs = $this->get('app.common.breadcrumbs');

        if( $id )
        {
            $product = $_manager->getRepository('AppBundle:Product\Product')->find($id);

            if( !$product )
                throw $this->createNotFoundException("Product identified by `id` {$id} not found");

            if( !$this->isGranted(ProductVoter::PRODUCT_READ, $product) )
                throw $this->createAccessDeniedException('Access denied');

            $response = [
                'view' => 'AppBundle:Entity/Product/CRUD:readItem.html.twig',
                'data' => ['product' => $product]
            ];

            $_breadcrumbs->add('product_read')->add('product_read', ['id' => $id], $_translator->trans('product_view', [], 'routes'));
        } else {
            if( !$_productBoundlessAccess->isGranted(ProductBoundlessAccess::PRODUCT_READ) )
                throw $this->createAccessDeniedException('Access denied');

            $product = $_manager->getRepository('AppBundle:Product\Product')->findAll();

            $response = [
                'view' => 'AppBundle:Entity/Product/CRUD:readList.html.twig',
                'data' => ['products' => $product]
            ];

            $_breadcrumbs->add('product_read');
        }

        return $this->render($response['view'], $response['data']);
    }

    /**
     * @Method({"GET", "POST"})
     * @Route(
     *      "/product/create",
     *      name="product_create",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"}
     * )
     */
    public function createAction(Request $request)
    {
        $_productBoundlessAccess = $this->get('app.security.product_boundless_access');

        if( !$_productBoundlessAccess->isGranted(ProductBoundlessAccess::PRODUCT_CREATE) )
            throw $this->createAccessDeniedException('Access denied');

        $_translator = $this->get('translator');

        $_breadcrumbs = $this->get('app.common.breadcrumbs');

        $_uploadedProductImageValidator = $this->get('app.validator.uploaded_product_image');

        $productType = new ProductType($_translator, $_productBoundlessAccess->isGranted(ProductBoundlessAccess::PRODUCT_CREATE));

        $form = $this->createForm($productType, $product = new Product, [
            'action' => $this->generateUrl('product_create')
        ]);

        $form->handleRequest($request);

        if( !($form->isValid() && $_uploadedProductImageValidator->validate($form))  ) {
            $_breadcrumbs->add('product_read')->add('product_create');

            return $this->render('AppBundle:Entity/Product/CRUD:createItem.html.twig', [
                'form' => $form->createView()
            ]);
        } else {
            $_manager = $this->getDoctrine()->getManager();

            // TODO: This large logic does not belong to Controller
            if( array_filter($form->getData()->getUploadedProductImages()) )
            {
                foreach ($form->getData()->getUploadedProductImages() as $uploadedProductImage)
                {
                    $productImage = (new ProductImage)
                        ->setImageProductFile($uploadedProductImage)
                        ->setUpdatedAt(new DateTime)
                    ;

                    $product->addProductImage($productImage);

                    $_manager->persist($productImage);
                }

                $_manager->persist($product);
            }

            $_manager->persist($product);
            $_manager->flush();

            if( $form->has('create_and_return') && $form->get('create_and_return')->isClicked() ) {
                return $this->redirectToRoute('product_read');
            } else {
                return $this->redirectToRoute('product_update', [
                    'id' => $product->getId()
                ]);
            }
        }
    }

    /**
     * @Method({"GET", "POST"})
     * @Route(
     *      "/product/update/{id}",
     *      name="product_update",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function updateAction(Request $request, $id)
    {
        $_manager = $this->getDoctrine()->getManager();

        $_productBoundlessAccess = $this->get('app.security.product_boundless_access');

        $_translator = $this->get('translator');

        $_breadcrumbs = $this->get('app.common.breadcrumbs');

        $_uploadedProductImageValidator = $this->get('app.validator.uploaded_product_image');

        $product = $_manager->getRepository('AppBundle:Product\Product')->find($id);

        if( !$product )
            throw $this->createNotFoundException("Product identified by `id` {$id} not found");

        if( !$this->isGranted(ProductVoter::PRODUCT_UPDATE, $product) ) {
            return $this->redirectToRoute('product_read', [
                'id' => $product->getId()
            ]);
        }

        $productType = new ProductType($_translator, $_productBoundlessAccess->isGranted(ProductBoundlessAccess::PRODUCT_CREATE));

        $form = $this->createForm($productType, $product, [
            'action' => $this->generateUrl('product_update', ['id' => $id])
        ]);

        $form->handleRequest($request);

        if( $form->isValid() && $_uploadedProductImageValidator->validate($form) )
        {
            // TODO: This large logic does not belong to Controller
            if( array_filter($form->getData()->getUploadedProductImages()) )
            {
                foreach( $product->getProductImages() as $productImage )
                {
                    $product->removeProductImage($productImage);

                    $_manager->remove($productImage);
                }

                foreach ($form->getData()->getUploadedProductImages() as $uploadedProductImage)
                {
                    $productImage = (new ProductImage)
                        ->setImageProductFile($uploadedProductImage)
                        ->setUpdatedAt(new DateTime)
                    ;

                    $product->addProductImage($productImage);

                    $_manager->persist($productImage);
                }

                $_manager->persist($product);
            }

            $_manager->flush();

            if( $form->has('update_and_return') && $form->get('update_and_return')->isClicked() ) {
                return $this->redirectToRoute('product_read');
            } else {
                return $this->redirectToRoute('product_update', [
                    'id' => $product->getId()
                ]);
            }
        }

        $_breadcrumbs->add('product_read')->add('product_update', ['id' => $id]);

        return $this->render('AppBundle:Entity/Product/CRUD:updateItem.html.twig', [
            'form'    => $form->createView(),
            'product' => $product
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/product/delete/{id}",
     *      name="product_delete",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function deleteAction($id)
    {
        $_manager = $this->getDoctrine()->getManager();

        $product = $_manager->getRepository('AppBundle:Product\Product')->find($id);

        if( !$product )
            throw $this->createNotFoundException("Product identified by `id` {$id} not found");

        if( !$this->isGranted(ProductVoter::PRODUCT_DELETE, $product) )
            throw $this->createAccessDeniedException('Access denied');

        $_manager->remove($product);
        $_manager->flush();

        return $this->redirectToRoute('product_read');
    }
}