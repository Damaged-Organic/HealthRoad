<?php
// AppBundle/Controller/Binding/ProductController.php
namespace AppBundle\Controller\Binding;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\RedirectResponse,
    Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;

use AppBundle\Controller\Utility\Traits\ClassOperationsTrait,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Entity\Supplier\Supplier,
    AppBundle\Entity\Product\ProductVendingGroup,
    AppBundle\Entity\Student\Student,
    AppBundle\Service\Security\ProductBoundlessAccess,
    AppBundle\Security\Authorization\Voter\ProductVoter,
    AppBundle\Security\Authorization\Voter\StudentVoter,
    AppBundle\Security\Authorization\Voter\ProductVendingGroupVoter;

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
                $object = $_manager->getRepository('AppBundle:Product\ProductVendingGroup')->find($objectId);

                if( !$object )
                    throw $this->createNotFoundException("Product Vending Group identified by `id` {$objectId} not found");

                $products = $object->getProducts();

                $action = [
                    'path'  => 'school_choose',
                    'voter' => ProductVendingGroupVoter::PRODUCT_VENDING_GROUP_BIND
                ];
            break;

            /*case $this->compareObjectClassNameToString(new Supplier, $objectClass):
                $supplier = $_manager->getRepository('AppBundle:Supplier\Supplier')->find($objectId);

                if( !$supplier )
                    throw $this->createNotFoundException("Supplier identified by `id` {$objectId} not found");

                $products = $supplier->getProducts();
            break;*/

            case $this->compareObjectClassNameToString(new Student, $objectClass):
                $object = $_manager->getRepository('AppBundle:Student\Student')->find($objectId);

                if( !$object )
                    throw $this->createNotFoundException("Student identified by `id` {$objectId} not found");

                $products = $object->getProducts();

                $action = [
                    'path'  => 'school_choose',
                    'voter' => StudentVoter::STUDENT_BIND
                ];
            break;

            default:
                throw new NotAcceptableHttpException("Object not supported");
            break;
        }

        return $this->render('AppBundle:Entity/Product/Binding:show.html.twig', [
            'standalone'  => TRUE,
            'products'    => $products,
            'object'      => $object,
            'action'      => $action
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

        $_translator = $this->get('translator');

        $_breadcrumbs = $this->get('app.common.breadcrumbs');

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new ProductVendingGroup, $objectClass):
                $productVendingGroup = $object = $_manager->getRepository('AppBundle:Product\ProductVendingGroup')->find($objectId);

                if( !$productVendingGroup )
                    throw $this->createNotFoundException("Product Vending Group identified by `id` {$objectId} not found");

                $products = $_manager->getRepository('AppBundle:Product\Product')->findAll();

                $path = 'product_vending_group_update_bounded';

                $_breadcrumbs->add('product_vending_group_read')->add('product_vending_group_update', ['id' => $objectId])->add('product_vending_group_update_bounded',
                    [
                        'objectId'    => $objectId,
                        'objectClass' => 'product'
                    ],
                    $_translator->trans('product_read', [], 'routes')
                );
            break;

            case $this->compareObjectClassNameToString(new Supplier, $objectClass):
                $supplier = $object = $_manager->getRepository('AppBundle:Supplier\Supplier')->find($objectId);

                if( !$supplier )
                    throw $this->createNotFoundException("Supplier identified by `id` {$objectId} not found");

                $products = $_manager->getRepository('AppBundle:Product\Product')->findAll();

                $path = 'supplier_update_bounded';

                $_breadcrumbs->add('supplier_read')->add('supplier_update', ['id' => $objectId])->add('supplier_update_bounded',
                    [
                        'objectId'    => $objectId,
                        'objectClass' => 'product'
                    ],
                    $_translator->trans('product_read', [], 'routes')
                );
            break;

            case $this->compareObjectClassNameToString(new Student, $objectClass):
                $student = $object = $_manager->getRepository('AppBundle:Student\Student')->find($objectId);

                if( !$student )
                    throw $this->createNotFoundException("Student identified by `id` {$objectId} not found");

                $products = $student->getNfcTag()->getVendingMachine()->getProductVendingGroup()->getProducts();

                $path = 'student_update_bounded';

                $_breadcrumbs->add('student_read')->add('student_update', ['id' => $objectId])->add('student_update_bounded',
                    [
                        'objectId'    => $objectId,
                        'objectClass' => 'product'
                    ],
                    $_translator->trans('product_read', [], 'routes')
                );
            break;

            default:
                throw new NotAcceptableHttpException("Object not supported");
            break;
        }

        $_breadcrumbs->add('product_choose', [
            'objectId'    => $objectId,
            'objectClass' => $objectClass,
        ]);

        return $this->render('AppBundle:Entity/School/Binding:choose.html.twig', [
            'path'     => $path,
            'products' => $products,
            'object'   => $object
        ]);
    }

    /**
     * @Method({"POST"})
     * @Route(
     *      "/product/bind/{targetId}/{objectClass}/{objectId}",
     *      name="product_bind",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "targetId" = "\d+", "objectClass" = "[a-z]+", "objectId" = "\d+"}
     * )
     */
    public function bindToAction(Request $request, $targetId, $objectClass, $objectId)
    {
        $_manager = $this->getDoctrine()->getManager();

        $_translator = $this->get('translator');

        $product = $_manager->getRepository('AppBundle:Product\Product')->find($targetId);

        if( !$product )
            throw $this->createNotFoundException($_translator->trans('common.error.not_found', [], 'responses'));

        if( !$this->isGranted(ProductVoter::PRODUCT_BIND, $product) )
            throw $this->createAccessDeniedException($_translator->trans('common.error.forbidden', [], 'responses'));

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new ProductVendingGroup, $objectClass):
                $productVendingGroup = $_manager->getRepository('AppBundle:Product\ProductVendingGroup')->find($objectId);

                if( !$productVendingGroup )
                    throw $this->createNotFoundException($_translator->trans('common.error.not_found', [], 'responses'));

                $productVendingGroup->addProduct($product);

                $_manager->persist($productVendingGroup);
            break;

            case $this->compareObjectClassNameToString(new Supplier, $objectClass):
                $supplier = $_manager->getRepository('AppBundle:Supplier\Supplier')->find($objectId);

                if( !$supplier )
                    throw $this->createNotFoundException($_translator->trans('common.error.not_found', [], 'responses'));

                $supplier->addProduct($product);

                $_manager->persist($supplier);
            break;

            case $this->compareObjectClassNameToString(new Student, $objectClass):
                $student = $_manager->getRepository('AppBundle:Student\Student')->find($objectId);

                if( !$student )
                    throw $this->createNotFoundException($_translator->trans('common.error.not_found', [], 'responses'));

                $student->addProduct($product);

                $_manager->persist($student);
            break;

            default:
                throw new NotAcceptableHttpException($_translator->trans('bind.error.not_boundalbe', [], 'responses'));
            break;
        }

        $_manager->flush();

        return new RedirectResponse($request->headers->get('referer'));
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/product/unbind/{targetId}/{objectClass}/{objectId}",
     *      name="product_unbind",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "targetId" = "\d+", "objectClass" = "[a-z]+", "objectId" = "\d+"}
     * )
     */
    public function unbindFromAction(Request $request, $targetId, $objectClass, $objectId)
    {
        $_manager = $this->getDoctrine()->getManager();

        $_translator = $this->get('translator');

        $product = $_manager->getRepository('AppBundle:Product\Product')->find($targetId);

        if( !$product )
            throw $this->createNotFoundException($_translator->trans('common.error.not_found', [], 'responses'));

        if( !$this->isGranted(ProductVoter::PRODUCT_BIND, $product) )
            throw $this->createAccessDeniedException($_translator->trans('common.error.forbidden', [], 'responses'));

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new ProductVendingGroup, $objectClass):
                $productVendingGroup = $_manager->getRepository('AppBundle:Product\ProductVendingGroup')->find($objectId);

                if( !$productVendingGroup )
                    throw $this->createNotFoundException($_translator->trans('common.error.not_found', [], 'responses'));

                $productVendingGroup->removeProduct($product);

                $_manager->persist($productVendingGroup);
            break;

            case $this->compareObjectClassNameToString(new Supplier, $objectClass):
                $product->setSupplier(NULL);
            break;

            case $this->compareObjectClassNameToString(new Student, $objectClass):
                $student = $_manager->getRepository('AppBundle:Student\Student')->find($objectId);

                if( !$student )
                    throw $this->createNotFoundException($_translator->trans('common.error.not_found', [], 'responses'));

                $student->removeProduct($product);

                $_manager->persist($student);
            break;

            default:
                throw new NotAcceptableHttpException($_translator->trans('bind.error.not_unboundalbe', [], 'responses'));
            break;
        }

        $_manager->flush();

        return new RedirectResponse($request->headers->get('referer'));
    }
}