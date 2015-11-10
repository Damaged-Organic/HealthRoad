<?php
// AppBundle/Controller/Binding/ProductController.php
namespace AppBundle\Controller\Binding;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\RedirectResponse,
    Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;

use JMS\DiExtraBundle\Annotation as DI;

use AppBundle\Controller\Utility\Traits\EntityFilter,
    AppBundle\Controller\Utility\Traits\ClassOperationsTrait,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Entity\Supplier\Supplier,
    AppBundle\Entity\Product\ProductVendingGroup,
    AppBundle\Entity\Student\Student,
    AppBundle\Service\Security\ProductBoundlessAccess,
    AppBundle\Security\Authorization\Voter\ProductVoter,
    AppBundle\Security\Authorization\Voter\StudentVoter,
    AppBundle\Security\Authorization\Voter\ProductVendingGroupVoter,
    AppBundle\Security\Authorization\Voter\SupplierVoter;

class ProductController extends Controller implements UserRoleListInterface
{
    use ClassOperationsTrait, EntityFilter;

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

    public function showAction($objectClass, $objectId)
    {
        if( !$this->_productBoundlessAccess->isGranted(ProductBoundlessAccess::PRODUCT_READ) )
            throw $this->createAccessDeniedException('Access denied');

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new ProductVendingGroup, $objectClass):
                $object = $this->_manager->getRepository('AppBundle:Product\ProductVendingGroup')->find($objectId);

                if( !$object )
                    throw $this->createNotFoundException("Product Vending Group identified by `id` {$objectId} not found");

                $action = [
                    'path'  => 'product_choose',
                    'voter' => ProductVendingGroupVoter::PRODUCT_VENDING_GROUP_BIND
                ];
            break;

            case $this->compareObjectClassNameToString(new Supplier, $objectClass):
                $object = $this->_manager->getRepository('AppBundle:Supplier\Supplier')->find($objectId);

                if( !$object )
                    throw $this->createNotFoundException("Supplier identified by `id` {$objectId} not found");

                $action = [
                    'path'  => 'product_choose',
                    'voter' => SupplierVoter::SUPPLIER_BIND
                ];
            break;

            case $this->compareObjectClassNameToString(new Student, $objectClass):
                $object = $this->_manager->getRepository('AppBundle:Student\Student')->find($objectId);

                if( !$object )
                    throw $this->createNotFoundException("Student identified by `id` {$objectId} not found");

                $action = [
                    'path'  => 'product_choose',
                    'voter' => StudentVoter::STUDENT_BIND
                ];
            break;

            default:
                throw new NotAcceptableHttpException("Object not supported");
            break;
        }

        $products = $this->filterDeletedIfNotGranted(
            ProductVoter::PRODUCT_READ,
            $object->getProducts()
        );

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
        if( !$this->_productBoundlessAccess->isGranted(ProductBoundlessAccess::PRODUCT_BIND) )
            throw $this->createAccessDeniedException('Access denied');

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new ProductVendingGroup, $objectClass):
                $productVendingGroup = $object = $this->_manager->getRepository('AppBundle:Product\ProductVendingGroup')->find($objectId);

                if( !$productVendingGroup )
                    throw $this->createNotFoundException("Product Vending Group identified by `id` {$objectId} not found");

                $products = $this->filterDeletedIfNotGranted(
                    ProductVoter::PRODUCT_READ,
                    $this->_manager->getRepository('AppBundle:Product\Product')->findAll()
                );

                $path = 'product_vending_group_update_bounded';

                $this->_breadcrumbs->add('product_vending_group_read')->add('product_vending_group_update', ['id' => $objectId])->add('product_vending_group_update_bounded',
                    [
                        'objectId'    => $objectId,
                        'objectClass' => 'product'
                    ],
                    $this->_translator->trans('product_read', [], 'routes')
                );
            break;

            case $this->compareObjectClassNameToString(new Supplier, $objectClass):
                $supplier = $object = $this->_manager->getRepository('AppBundle:Supplier\Supplier')->find($objectId);

                if( !$supplier )
                    throw $this->createNotFoundException("Supplier identified by `id` {$objectId} not found");

                $products = $this->filterDeletedIfNotGranted(
                    ProductVoter::PRODUCT_READ,
                    $this->_manager->getRepository('AppBundle:Product\Product')->findAll()
                );

                $path = 'supplier_update_bounded';

                $this->_breadcrumbs->add('supplier_read')->add('supplier_update', ['id' => $objectId])->add('supplier_update_bounded',
                    [
                        'objectId'    => $objectId,
                        'objectClass' => 'product'
                    ],
                    $this->_translator->trans('product_read', [], 'routes')
                );
            break;

            case $this->compareObjectClassNameToString(new Student, $objectClass):
                $student = $object = $this->_manager->getRepository('AppBundle:Student\Student')->find($objectId);

                if( !$student )
                    throw $this->createNotFoundException("Student identified by `id` {$objectId} not found");

                $products = $this->filterDeletedIfNotGranted(
                    ProductVoter::PRODUCT_READ,
                    $this->_manager->getRepository('AppBundle:Product\Product')->findAvailableByStudent($student)
                );

                $path = 'student_update_bounded';

                $this->_breadcrumbs->add('student_read')->add('student_update', ['id' => $objectId])->add('student_update_bounded',
                    [
                        'objectId'    => $objectId,
                        'objectClass' => 'product'
                    ],
                    $this->_translator->trans('product_read', [], 'routes')
                );
            break;

            default:
                throw new NotAcceptableHttpException("Object not supported");
            break;
        }

        $this->_breadcrumbs->add('product_choose', [
            'objectId'    => $objectId,
            'objectClass' => $objectClass,
        ]);

        return $this->render('AppBundle:Entity/Product/Binding:choose.html.twig', [
            'path'     => $path,
            'products' => $products,
            'object'   => $object
        ]);
    }

    /**
     * @Method({"GET"})
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
        $product = $this->_manager->getRepository('AppBundle:Product\Product')->find($targetId);

        if( !$product )
            throw $this->createNotFoundException($this->_translator->trans('common.error.not_found', [], 'responses'));

        if( !$this->isGranted(ProductVoter::PRODUCT_BIND, $product) )
            throw $this->createAccessDeniedException($this->_translator->trans('common.error.forbidden', [], 'responses'));

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new ProductVendingGroup, $objectClass):
                $productVendingGroup = $this->_manager->getRepository('AppBundle:Product\ProductVendingGroup')->find($objectId);

                if( !$productVendingGroup )
                    throw $this->createNotFoundException($this->_translator->trans('common.error.not_found', [], 'responses'));

                $productVendingGroup->addProduct($product);

                $this->_manager->persist($productVendingGroup);
            break;

            case $this->compareObjectClassNameToString(new Supplier, $objectClass):
                $supplier = $this->_manager->getRepository('AppBundle:Supplier\Supplier')->find($objectId);

                if( !$supplier )
                    throw $this->createNotFoundException($this->_translator->trans('common.error.not_found', [], 'responses'));

                $supplier->addProduct($product);

                $this->_manager->persist($supplier);
            break;

            case $this->compareObjectClassNameToString(new Student, $objectClass):
                $student = $this->_manager->getRepository('AppBundle:Student\Student')->find($objectId);

                if( !$student )
                    throw $this->createNotFoundException($this->_translator->trans('common.error.not_found', [], 'responses'));

                $student->addProduct($product);

                $this->_manager->persist($student);
            break;

            default:
                throw new NotAcceptableHttpException($this->_translator->trans('bind.error.not_boundalbe', [], 'responses'));
            break;
        }

        $this->_manager->flush();

        $this->_messages->markBindSuccess(
            $this->_translator->trans('bind.success.product', [], 'responses')
        );

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
        $product = $this->_manager->getRepository('AppBundle:Product\Product')->find($targetId);

        if( !$product )
            throw $this->createNotFoundException($this->_translator->trans('common.error.not_found', [], 'responses'));

        if( !$this->isGranted(ProductVoter::PRODUCT_BIND, $product) )
            throw $this->createAccessDeniedException($this->_translator->trans('common.error.forbidden', [], 'responses'));

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new ProductVendingGroup, $objectClass):
                $productVendingGroup = $this->_manager->getRepository('AppBundle:Product\ProductVendingGroup')->find($objectId);

                if( !$productVendingGroup )
                    throw $this->createNotFoundException($this->_translator->trans('common.error.not_found', [], 'responses'));

                $productVendingGroup->removeProduct($product);

                $this->_manager->persist($productVendingGroup);
            break;

            case $this->compareObjectClassNameToString(new Supplier, $objectClass):
                $product->setSupplier(NULL);
            break;

            case $this->compareObjectClassNameToString(new Student, $objectClass):
                $student = $this->_manager->getRepository('AppBundle:Student\Student')->find($objectId);

                if( !$student )
                    throw $this->createNotFoundException($this->_translator->trans('common.error.not_found', [], 'responses'));

                $student->removeProduct($product);

                $this->_manager->persist($student);
            break;

            default:
                throw new NotAcceptableHttpException($this->_translator->trans('bind.error.not_unboundalbe', [], 'responses'));
            break;
        }

        $this->_manager->flush();

        $this->_messages->markUnbindSuccess(
            $this->_translator->trans('unbind.success.product', [], 'responses')
        );

        return new RedirectResponse($request->headers->get('referer'));
    }
}