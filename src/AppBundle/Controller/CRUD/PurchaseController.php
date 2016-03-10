<?php
// AppBundle/Controller/CRUD/PurchaseController.php
namespace AppBundle\Controller\CRUD;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use JMS\DiExtraBundle\Annotation as DI;

use AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Security\Authorization\Voter\PurchaseVoter,
    AppBundle\Service\Security\PurchaseBoundlessAccess;

class PurchaseController extends Controller implements UserRoleListInterface
{
    /** @DI\Inject("doctrine.orm.entity_manager") */
    private $_manager;

    /** @DI\Inject("translator") */
    private $_translator;

    /** @DI\Inject("app.common.breadcrumbs") */
    private $_breadcrumbs;

    /** @DI\Inject("app.security.purchase_boundless_access") */
    private $_purchaseBoundlessAccess;

    /**
     * @Method({"GET"})
     * @Route(
     *      "/purchase/{id}",
     *      name="purchase_read",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = null},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function readAction($id = NULL)
    {
        if( $id )
        {
            $purchase = $this->_manager->getRepository('AppBundle:Purchase\Purchase')->find($id);

            if( !$purchase )
                throw $this->createNotFoundException("Purchase identified by `id` {$id} not found");

            if( !$this->isGranted(PurchaseVoter::PURCHASE_READ, $purchase) )
                throw $this->createAccessDeniedException('Access denied');

            $response = [
                'view' => 'AppBundle:Entity/Purchase/CRUD:readItem.html.twig',
                'data' => ['purchase' => $purchase]
            ];

            $this->_breadcrumbs->add('purchase_read')->add('purchase_read', ['id' => $id], $this->_translator->trans('purchase_view', [], 'routes'));
        } else {
            if( !$this->_purchaseBoundlessAccess->isGranted(PurchaseBoundlessAccess::PURCHASE_READ) )
                throw $this->createAccessDeniedException('Access denied');

            $purchases = $this->_manager->getRepository('AppBundle:Purchase\Purchase')->findBy([], ['syncPurchasedAt' => 'DESC']);

            $response = [
                'view' => 'AppBundle:Entity/Purchase/CRUD:readList.html.twig',
                'data' => ['purchases' => $purchases]
            ];

            $this->_breadcrumbs->add('purchase_read');
        }

        return $this->render($response['view'], $response['data']);
    }
}
