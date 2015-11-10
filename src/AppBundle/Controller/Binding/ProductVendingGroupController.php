<?php
// AppBundle/Controller/Binding/ProductVendingGroupController.php
namespace AppBundle\Controller\Binding;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException,
    Symfony\Bundle\FrameworkBundle\Controller\Controller;

use JMS\DiExtraBundle\Annotation as DI;

use AppBundle\Controller\Utility\Traits\EntityFilter,
    AppBundle\Controller\Utility\Traits\ClassOperationsTrait,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Entity\Product\Product,
    AppBundle\Entity\VendingMachine\VendingMachine,
    AppBundle\Security\Authorization\Voter\ProductVendingGroupVoter;

class ProductVendingGroupController extends Controller implements UserRoleListInterface
{
    use ClassOperationsTrait, EntityFilter;

    /** @DI\Inject("doctrine.orm.entity_manager") */
    private $_manager;

    /** @DI\Inject("translator") */
    private $_translator;

    /** @DI\Inject("app.common.breadcrumbs") */
    private $_breadcrumbs;

    /**
     * @Method({"GET"})
     * @Route(
     *      "/product_vending_group/update/{objectId}/bounded/{objectClass}",
     *      name="product_vending_group_update_bounded",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "objectId" = "\d+", "objectClass" = "[a-z]+"}
     * )
     */
    public function boundedAction($objectId, $objectClass)
    {
        $productVendingGroup = $this->_manager->getRepository('AppBundle:Product\ProductVendingGroup')->find($objectId);

        if( !$productVendingGroup )
            throw $this->createNotFoundException("Product Vending Group identified by `id` {$objectId} not found");

        if( !$this->isGranted(ProductVendingGroupVoter::PRODUCT_VENDING_GROUP_READ, $productVendingGroup) )
            throw $this->createAccessDeniedException('Access denied');

        $this->_breadcrumbs->add('product_vending_group_read')->add('product_vending_group_update', ['id' => $objectId], $this->_translator->trans('product_vending_group_bounded', [], 'routes'));

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new VendingMachine, $objectClass):
                $bounded = $this->forward('AppBundle:Binding\VendingMachine:show', [
                    'objectClass' => $this->getObjectClassName($productVendingGroup),
                    'objectId'    => $objectId
                ]);

                $this->_breadcrumbs->add('product_vending_group_update_bounded',
                    [
                        'objectId'    => $objectId,
                        'objectClass' => $objectClass
                    ],
                    $this->_translator->trans('vending_machine_read', [], 'routes')
                );
            break;

            case $this->compareObjectClassNameToString(new Product, $objectClass):
                $bounded = $this->forward('AppBundle:Binding\Product:show', [
                    'objectClass' => $this->getObjectClassName($productVendingGroup),
                    'objectId'    => $objectId
                ]);

                $this->_breadcrumbs->add('product_vending_group_update_bounded',
                    [
                        'objectId'    => $objectId,
                        'objectClass' => $objectClass
                    ],
                    $this->_translator->trans('product_read', [], 'routes')
                );
            break;

            default:
                throw new NotAcceptableHttpException("Object not supported");
            break;
        }

        return $this->render('AppBundle:Entity/ProductVendingGroup/Binding:bounded.html.twig', [
            'objectClass'         => $objectClass,
            'bounded'             => $bounded->getContent(),
            'productVendingGroup' => $productVendingGroup
        ]);
    }
}