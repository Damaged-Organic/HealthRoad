<?php
// src/AppBundle/Controller/CRUD/PurchaseServiceController.php
namespace AppBundle\Controller\CRUD;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use JMS\DiExtraBundle\Annotation as DI;

use AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Security\Authorization\Voter\PurchaseServiceVoter,
    AppBundle\Service\Security\PurchaseServiceBoundlessAccess;

class PurchaseServiceController extends Controller implements UserRoleListInterface
{
    /** @DI\Inject("doctrine.orm.entity_manager") */
    private $_manager;

    /** @DI\Inject("translator") */
    private $_translator;

    /** @DI\Inject("app.common.breadcrumbs") */
    private $_breadcrumbs;

    /** @DI\Inject("app.security.purchase_service_boundless_access") */
    private $_purchaseServiceBoundlessAccess;

    /**
     * @Method({"GET"})
     * @Route(
     *      "/purchase_service/{id}",
     *      name="purchase_service_read",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = null},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function readAction($id = NULL)
    {
        if( $id )
        {
            $purchaseService = $this->_manager->getRepository('AppBundle:PurchaseService\PurchaseService')->find($id);

            if( !$purchaseService )
                throw $this->createNotFoundException("Purchase Service identified by `id` {$id} not found");

            if( !$this->isGranted(PurchaseServiceVoter::PURCHASE_SERVICE_READ, $purchaseService) )
                throw $this->createAccessDeniedException('Access denied');

            $response = [
                'view' => 'AppBundle:Entity/PurchaseService/CRUD:readItem.html.twig',
                'data' => ['purchaseService' => $purchaseService]
            ];

            $this->_breadcrumbs
                ->add('purchase_read')
                ->add('purchase_service_read', [], $this->_translator->trans('purchase_service_read', [], 'routes'))
                ->add('purchase_service_read', ['id' => $id], $this->_translator->trans('purchase_service_view', [], 'routes'))
            ;
        } else {
            if( !$this->_purchaseServiceBoundlessAccess->isGranted(PurchaseServiceBoundlessAccess::PURCHASE_SERVICE_READ) )
                throw $this->createAccessDeniedException('Access denied');

            $purchasesService = $this->_manager->getRepository('AppBundle:PurchaseService\PurchaseService')->findBy([], ['purchasedAt' => 'DESC']);

            $response = [
                'view' => 'AppBundle:Entity/PurchaseService/CRUD:readList.html.twig',
                'data' => ['purchasesService' => $purchasesService]
            ];

            $this->_breadcrumbs
                ->add('purchase_read')
                ->add('purchase_service_read', [], $this->_translator->trans('purchase_service_read', [], 'routes'))
            ;
        }

        return $this->render($response['view'], $response['data']);
    }
}
