<?php
// AppBundle/Controller/CRUD/PurchaseController.php
namespace AppBundle\Controller\CRUD;

use AppBundle\Service\Security\PurchaseBoundlessAccess;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Security\Authorization\Voter\PurchaseVoter;

class PurchaseController extends Controller implements UserRoleListInterface
{
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
        $_manager = $this->getDoctrine()->getManager();

        $_purchaseBoundlessAccess = $this->get('app.security.purchase_boundless_access');

        $_translator = $this->get('translator');

        $_breadcrumbs = $this->get('app.common.breadcrumbs');

        if( $id )
        {
            $purchase = $_manager->getRepository('AppBundle:Purchase\Purchase')->find($id);

            if( !$purchase )
                throw $this->createNotFoundException("Purchase identified by `id` {$id} not found");

            if( !$this->isGranted(PurchaseVoter::PURCHASE_READ, $purchase) )
                throw $this->createAccessDeniedException('Access denied');

            $response = [
                'view' => 'AppBundle:Entity/Purchase/CRUD:readItem.html.twig',
                'data' => ['purchase' => $purchase]
            ];

            $_breadcrumbs->add('purchase_read')->add('purchase_read', ['id' => $id], $_translator->trans('purchase_view', [], 'routes'));
        } else {
            if( !$_purchaseBoundlessAccess->isGranted(PurchaseBoundlessAccess::PURCHASE_READ) )
                throw $this->createAccessDeniedException('Access denied');

            $purchases = $_manager->getRepository('AppBundle:Purchase\Purchase')->findAll();

            $response = [
                'view' => 'AppBundle:Entity/Purchase/CRUD:readList.html.twig',
                'data' => ['purchases' => $purchases]
            ];

            $_breadcrumbs->add('purchase_read');
        }

        return $this->render($response['view'], $response['data']);
    }
}