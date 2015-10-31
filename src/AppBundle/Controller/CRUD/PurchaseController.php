<?php
// AppBundle/Controller/CRUD/PurchaseController.php
namespace AppBundle\Controller\CRUD;

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
    public function readAction($id)
    {
        $_manager = $this->getDoctrine()->getManager();

        $_translator = $this->get('translator');

        $_breadcrumbs = $this->get('app.common.breadcrumbs');

        $purchase = $_manager->getRepository('AppBundle:Purchase\Purchase')->find($id);

        if( !$purchase )
            throw $this->createNotFoundException("Purchase identified by `id` {$id} not found");

        if( !$this->isGranted(PurchaseVoter::PURCHASE_READ, $purchase) )
            throw $this->createAccessDeniedException('Access denied');

        $response = [
            'view' => 'AppBundle:Entity/Purchase/CRUD:readItem.html.twig',
            'data' => ['purchase' => $purchase]
        ];

        // TODO: Breadcrumbs for referral object read request. Probably should be implemented in other entities as well.
        $_breadcrumbs
            ->add('vending_machine_read')
            ->add('vending_machine_update',
                [
                    'id' => $purchase->getVendingMachine()->getId()
                ],
                $_translator->trans('vending_machine_bounded', [], 'routes')
            )
            ->add('vending_machine_update_bounded',
                [
                    'objectId'    => $purchase->getVendingMachine()->getId(),
                    'objectClass' => 'purchase'
                ],
                $_translator->trans('purchase_read', [], 'routes')
            )
            ->add('purchase_read',
                [
                    'id' => $id
                ],
                $_translator->trans('purchase_view', [], 'routes')
            )
        ;

        return $this->render($response['view'], $response['data']);
    }
}