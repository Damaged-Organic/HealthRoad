<?php
// AppBundle/Controller/Binding/ProductController.php
namespace AppBundle\Controller\Binding;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;

use AppBundle\Controller\Traits\ClassOperationsTrait,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Service\Security\ProductBoundlessAccess,
    AppBundle\Security\Authorization\Voter\ProductVoter,
    AppBundle\Entity\Supplier\Supplier,
    AppBundle\Entity\Product\ProductVendingGroup,
    AppBundle\Entity\Student\Student;

class ProductController extends Controller implements UserRoleListInterface
{
    use ClassOperationsTrait;

    public function showAction($objectClass, $objectId)
    {
        $_productBoundlessAccess = $this->get('app.security.product_boundless_access');

        if( !$_productBoundlessAccess->isGranted(ProductBoundlessAccess::PRODUCT_READ) )
            throw $this->createAccessDeniedException('Access denied');

        $_manager = $this->getDoctrine()->getManager();

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new ProductVendingGroup, $objectClass):
                $productVendingGroup = $_manager->getRepository('AppBundle:Product\ProductVendingGroup')->find($objectId);

                if( !$productVendingGroup )
                    throw $this->createNotFoundException("Product Vending Group identified by `id` {$objectId} not found");

                $products = $productVendingGroup->getProducts();
            break;

            case $this->compareObjectClassNameToString(new Supplier, $objectClass):
                $supplier = $_manager->getRepository('AppBundle:Supplier\Supplier')->find($objectId);

                if( !$supplier )
                    throw $this->createNotFoundException("Supplier identified by `id` {$objectId} not found");

                $products = $supplier->getProducts();
            break;

            case $this->compareObjectClassNameToString(new Student, $objectClass):
                $student = $_manager->getRepository('AppBundle:Student\Student')->find($objectId);

                if( !$student )
                    throw $this->createNotFoundException("Student identified by `id` {$objectId} not found");

                $products = $student->getProducts();
            break;

            default:
                throw new NotAcceptableHttpException("Object not supported");
            break;
        }

        return $this->render('AppBundle:Entity/Product/Binding:show.html.twig', [
            'products'    => $products,
            'objectId'    => $objectId,
            'objectClass' => $objectClass
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/product/choose_for/{objectClass}/{objectId}",
     *      name="product_choose",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "objectClass" = "[a-z]+", "objectId" = "\d+"}
     * )
     */
    public function chooseAction($objectClass, $objectId)
    {
        $_productBoundlessAccess = $this->get('app.security.product_boundless_access');

        if( !$_productBoundlessAccess->isGranted(ProductBoundlessAccess::PRODUCT_BIND) )
            throw $this->createAccessDeniedException('Access denied');

        $_manager = $this->getDoctrine()->getManager();

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new ProductVendingGroup, $objectClass):
                $productVendingGroup = $_manager->getRepository('AppBundle:Product\ProductVendingGroup')->find($objectId);

                if( !$productVendingGroup )
                    throw $this->createNotFoundException("Product Vending Group identified by `id` {$objectId} not found");

                $object = [
                    'class' => $this->getObjectClassName($productVendingGroup),
                    'id'    => $productVendingGroup->getId()
                ];

                $products = $_manager->getRepository('AppBundle:Product\Product')->findAll();
            break;

            case $this->compareObjectClassNameToString(new Supplier, $objectClass):
                $supplier = $_manager->getRepository('AppBundle:Supplier\Supplier')->find($objectId);

                if( !$supplier )
                    throw $this->createNotFoundException("Supplier identified by `id` {$objectId} not found");

                $object = [
                    'class' => $this->getObjectClassName($supplier),
                    'id'    => $supplier->getId()
                ];

                $products = $_manager->getRepository('AppBundle:Product\Product')->findAll();
            break;

            case $this->compareObjectClassNameToString(new Student, $objectClass):
                $student = $_manager->getRepository('AppBundle:Student\Student')->find($objectId);

                if( !$student )
                    throw $this->createNotFoundException("Student identified by `id` {$objectId} not found");

                $object = [
                    'class' => $this->getObjectClassName($student),
                    'id'    => $student->getId()
                ];

                $products = $student->getNfcTag()->getVendingMachine()->getProductVendingGroup()->getProducts();
            break;

            default:
                throw new NotAcceptableHttpException("Object not supported");
            break;
        }

        return $this->render('AppBundle:Entity/Product/Binding:choose.html.twig', [
            'products'    => $products,
            'objectClass' => $object['class'],
            'objectId'    => $object['id']
        ]);
    }

    /**
     * @Method({"POST"})
     * @Route(
     *      "/product/bind",
     *      name="product_bind",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"}
     * )
     */
    public function bindToAction(Request $request)
    {
        $productId = ( $request->request->has('productId') ) ? $request->request->get('productId') : NULL;

        $_manager = $this->getDoctrine()->getManager();

        $product = $_manager->getRepository('AppBundle:Product\Product')->find($productId);

        if( !$product )
            throw $this->createNotFoundException("Product identified by `id` {$productId} not found");

        if( !$this->isGranted(ProductVoter::PRODUCT_BIND, $product) )
            throw $this->createAccessDeniedException('Access denied');

        $objectClass = ( $request->request->get('objectClass') ) ? $request->request->get('objectClass') : NULL;
        $objectId    = ( $request->request->get('objectId') ) ? $request->request->get('objectId') : NULL;

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new ProductVendingGroup, $objectClass):
                $productVendingGroup = $_manager->getRepository('AppBundle:Product\ProductVendingGroup')->find($objectId);

                if( !$productVendingGroup )
                    throw $this->createNotFoundException("Product Vending Group identified by `id` {$objectId} not found");

                $productVendingGroup->addProduct($product);

                $_manager->persist($productVendingGroup);

                $redirect = [
                    'route' => "product_vending_group_update",
                    'id'    => $productVendingGroup->getId()
                ];
            break;

            case $this->compareObjectClassNameToString(new Supplier, $objectClass):
                $supplier = $_manager->getRepository('AppBundle:Supplier\Supplier')->find($objectId);

                if( !$supplier )
                    throw $this->createNotFoundException("Supplier identified by `id` {$objectId} not found");

                $supplier->addProduct($product);

                $_manager->persist($supplier);

                $redirect = [
                    'route' => "supplier_update",
                    'id'    => $supplier->getId()
                ];
            break;

            case $this->compareObjectClassNameToString(new Student, $objectClass):
                $student = $_manager->getRepository('AppBundle:Student\Student')->find($objectId);

                if( !$student )
                    throw $this->createNotFoundException("Student identified by `id` {$objectId} not found");

                $student->addProduct($product);

                $_manager->persist($student);

                $redirect = [
                    'route' => "student_update",
                    'id'    => $student->getId()
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
     *      "/product/unbind/{id}/{objectClass}/{objectId}",
     *      name="product_unbind",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+", "objectClass" = "[a-z]+", "objectId" = "\d+"}
     * )
     */
    public function unbindFromAction($id, $objectClass, $objectId)
    {
        $_manager = $this->getDoctrine()->getManager();

        $product = $_manager->getRepository('AppBundle:Product\Product')->find($id);

        if( !$product )
            throw $this->createNotFoundException("Product identified by `id` {$id} not found");

        if( !$this->isGranted(ProductVoter::PRODUCT_BIND, $product) )
            throw $this->createAccessDeniedException('Access denied');

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new ProductVendingGroup, $objectClass):
                $productVendingGroup = $_manager->getRepository('AppBundle:Product\ProductVendingGroup')->find($objectId);

                if( !$productVendingGroup )
                    throw $this->createNotFoundException("Product Vending Group identified by `id` {$objectId} not found");

                $productVendingGroup->removeProduct($product);

                $_manager->persist($productVendingGroup);

                $redirect = [
                    'route' => "product_vending_group_update",
                    'id'    => $productVendingGroup->getId()
                ];
            break;

            case $this->compareObjectClassNameToString(new Supplier, $objectClass):
                //this should be gone in AJAX version
                $supplierId = $product->getSupplier()->getId();

                $product->setSupplier(NULL);

                $redirect = [
                    'route' => "supplier_update",
                    'id'    => $supplierId
                ];
            break;

            case $this->compareObjectClassNameToString(new Student, $objectClass):
                $student = $_manager->getRepository('AppBundle:Student\Student')->find($objectId);

                if( !$student )
                    throw $this->createNotFoundException("Student identified by `id` {$objectId} not found");

                $student->removeProduct($product);

                $_manager->persist($student);

                $redirect = [
                    'route' => "student_update",
                    'id'    => $student->getId()
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