<?php
// AppBundle/Controller/Binding/SupplierController.php
namespace AppBundle\Controller\Binding;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;

use JMS\DiExtraBundle\Annotation as DI;

use AppBundle\Controller\Utility\Traits\ClassOperationsTrait,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Entity\Product\Product,
    AppBundle\Security\Authorization\Voter\SupplierVoter;

class SupplierController extends Controller implements UserRoleListInterface
{
    use ClassOperationsTrait;

    /** @DI\Inject("doctrine.orm.entity_manager") */
    private $_manager;

    /** @DI\Inject("translator") */
    private $_translator;

    /** @DI\Inject("app.common.breadcrumbs") */
    private $_breadcrumbs;

    /**
     * @Method({"GET"})
     * @Route(
     *      "/supplier/update/{objectId}/bounded/{objectClass}",
     *      name="supplier_update_bounded",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "objectId" = "\d+", "objectClass" = "[a-z]+"}
     * )
     */
    public function boundedAction($objectId, $objectClass)
    {
        $supplier = $this->_manager->getRepository('AppBundle:Supplier\Supplier')->find($objectId);

        if( !$supplier )
            throw $this->createNotFoundException("Supplier identified by `id` {$objectId} not found");

        if( !$this->isGranted(SupplierVoter::SUPPLIER_READ, $supplier) )
            throw $this->createAccessDeniedException('Access denied');

        $this->_breadcrumbs->add('supplier_read')->add('supplier_update', ['id' => $objectId], $this->_translator->trans('supplier_bounded', [], 'routes'));

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Product, $objectClass):
                $bounded = $this->forward('AppBundle:Binding\Product:show', [
                    'objectClass' => $this->getObjectClassName($supplier),
                    'objectId'    => $objectId
                ]);

                $this->_breadcrumbs->add('supplier_update_bounded',
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

        return $this->render('AppBundle:Entity/Supplier/Binding:bounded.html.twig', [
            'objectClass' => $objectClass,
            'bounded'     => $bounded->getContent(),
            'supplier'    => $supplier
        ]);
    }
}
