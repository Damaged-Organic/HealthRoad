<?php
// AppBundle/Controller/CRUD/ProductController.php
namespace AppBundle\Controller\CRUD;

use DateTime;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Bundle\FrameworkBundle\Controller\Controller;

use JMS\DiExtraBundle\Annotation as DI;

use AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Entity\Product\Product,
    AppBundle\Entity\Product\ProductImage,
    AppBundle\Form\Type\ProductType,
    AppBundle\Security\Authorization\Voter\ProductVoter,
    AppBundle\Service\Security\ProductBoundlessAccess;

class ProductController extends Controller implements UserRoleListInterface
{
    /** @DI\Inject("doctrine.orm.entity_manager") */
    private $_manager;

    /** @DI\Inject("translator") */
    private $_translator;

    /** @DI\Inject("app.common.breadcrumbs") */
    private $_breadcrumbs;

    /** @DI\Inject("app.common.messages") */
    private $_messages;

    /** @DI\Inject("app.security.product_boundless_access") */
    private $_productBoundlessAccess;

    /** @DI\Inject("app.validator.uploaded_product_image") */
    private $_uploadedProductImageValidator;

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
        if( $id )
        {
            $product = $this->_manager->getRepository('AppBundle:Product\Product')->find($id);

            if( !$product )
                throw $this->createNotFoundException("Product identified by `id` {$id} not found");

            if( !$this->isGranted(ProductVoter::PRODUCT_READ, $product) )
                throw $this->createAccessDeniedException('Access denied');

            $response = [
                'view' => 'AppBundle:Entity/Product/CRUD:readItem.html.twig',
                'data' => ['product' => $product]
            ];

            $this->_breadcrumbs->add('product_read')->add('product_read', ['id' => $id], $this->_translator->trans('product_view', [], 'routes'));
        } else {
            if( !$this->_productBoundlessAccess->isGranted(ProductBoundlessAccess::PRODUCT_READ) )
                throw $this->createAccessDeniedException('Access denied');

            $product = $this->_manager->getRepository('AppBundle:Product\Product')->findAll();

            $response = [
                'view' => 'AppBundle:Entity/Product/CRUD:readList.html.twig',
                'data' => ['products' => $product]
            ];

            $this->_breadcrumbs->add('product_read');
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
        if( !$this->_productBoundlessAccess->isGranted(ProductBoundlessAccess::PRODUCT_CREATE) )
            throw $this->createAccessDeniedException('Access denied');

        $productType = new ProductType($this->_translator, $this->_productBoundlessAccess->isGranted(ProductBoundlessAccess::PRODUCT_CREATE));

        $form = $this->createForm($productType, $product = new Product, [
            'action' => $this->generateUrl('product_create')
        ]);

        $form->handleRequest($request);

        if( !($form->isValid() && $this->_uploadedProductImageValidator->validate($form))  ) {
            $this->_breadcrumbs->add('product_read')->add('product_create');

            return $this->render('AppBundle:Entity/Product/CRUD:createItem.html.twig', [
                'form' => $form->createView()
            ]);
        } else {
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

                    $this->_manager->persist($productImage);
                }

                $this->_manager->persist($product);
            }

            $this->_manager->persist($product);
            $this->_manager->flush();

            $this->_messages->markCreateSuccess();

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
        $product = $this->_manager->getRepository('AppBundle:Product\Product')->find($id);

        if( !$product )
            throw $this->createNotFoundException("Product identified by `id` {$id} not found");

        if( !$this->isGranted(ProductVoter::PRODUCT_UPDATE, $product) ) {
            return $this->redirectToRoute('product_read', [
                'id' => $product->getId()
            ]);
        }

        $productType = new ProductType($this->_translator, $this->_productBoundlessAccess->isGranted(ProductBoundlessAccess::PRODUCT_CREATE));

        $form = $this->createForm($productType, $product, [
            'action' => $this->generateUrl('product_update', ['id' => $id])
        ]);

        $form->handleRequest($request);

        if( $form->isValid() && $this->_uploadedProductImageValidator->validate($form) )
        {
            // TODO: This large logic does not belong to Controller
            if( array_filter($form->getData()->getUploadedProductImages()) )
            {
                foreach( $product->getProductImages() as $productImage )
                {
                    $product->removeProductImage($productImage);

                    $this->_manager->remove($productImage);
                }

                foreach ($form->getData()->getUploadedProductImages() as $uploadedProductImage)
                {
                    $productImage = (new ProductImage)
                        ->setImageProductFile($uploadedProductImage)
                        ->setUpdatedAt(new DateTime)
                    ;

                    $product->addProductImage($productImage);

                    $this->_manager->persist($productImage);
                }

                $this->_manager->persist($product);
            }

            $this->_manager->flush();

            $this->_messages->markUpdateSuccess();

            if( $form->has('update_and_return') && $form->get('update_and_return')->isClicked() ) {
                return $this->redirectToRoute('product_read');
            } else {
                return $this->redirectToRoute('product_update', [
                    'id' => $product->getId()
                ]);
            }
        }

        $this->_breadcrumbs->add('product_read')->add('product_update', ['id' => $id]);

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
        $product = $this->_manager->getRepository('AppBundle:Product\Product')->find($id);

        if( !$product )
            throw $this->createNotFoundException("Product identified by `id` {$id} not found");

        if( !$this->isGranted(ProductVoter::PRODUCT_DELETE, $product) )
            throw $this->createAccessDeniedException('Access denied');

        $this->_manager->remove($product);
        $this->_manager->flush();

        $this->_messages->markDeleteSuccess();

        return $this->redirectToRoute('product_read');
    }
}