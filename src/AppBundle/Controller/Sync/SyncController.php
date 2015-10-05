<?php
// AppBundle/Controller/Sync/SyncController.php
namespace AppBundle\Controller\Sync;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\JsonResponse,
    Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException,
    Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use AppBundle\Entity\Product\Product;

use AppBundle\Entity\NfcTag\Utility\Interfaces\SyncNfcTagPropertiesInterface,
    AppBundle\Entity\VendingMachine\Utility\Interfaces\SyncVendingMachinePropertiesInterface,
    AppBundle\Entity\VendingMachine\Utility\Interfaces\SyncVendingMachineEventPropertiesInterface,
    AppBundle\Entity\Purchase\Utility\Interfaces\SyncPurchasePropertiesInterface;

class SyncController extends Controller implements
    SyncNfcTagPropertiesInterface,
    SyncVendingMachinePropertiesInterface,
    SyncVendingMachineEventPropertiesInterface,
    SyncPurchasePropertiesInterface
{
    const SYNC_DATA     = 'data';
    const SYNC_CHECKSUM = 'checksum';

    /**
     * @Method({"GET"})
     * @Route(
     *      "/vending_machines/{serial}/products",
     *      name = "sync_get_vending_machines_products",
     *      host = "{domain_sync_v1}",
     *      defaults = { "_locale" = "%locale%", "domain_sync_v1" = "%domain_sync_v1%" },
     *      requirements = { "_locale" = "%locale%", "domain_sync_v1" = "%domain_sync_v1%" }
     * )
     */
    public function getProductsAction(Request $request, $serial)
    {
        /*file_put_contents(
            'test.txt',
            print_r([
                'login'    => $request->query->get('login'),
                'password' => $request->query->get('password')
            ], TRUE),
            FILE_APPEND
        );*/

        $_manager = $this->getDoctrine()->getManager();

        $_authentication = $this->get('app.sync.security.authentication');

        $vendingMachine = $_manager->getRepository('AppBundle:VendingMachine\VendingMachine')->findOneBy(['serial' => $serial]);

        if( !$vendingMachine )
            throw new NotFoundHttpException("Vending Machine identified by `id` {$serial} not found");

        if( !$_authentication->authenticate($request, $vendingMachine) )
            throw new AccessDeniedHttpException('Access denied');

        $products = $vendingMachine->getProductVendingGroup()->getProducts();

        $productsData = [];
        foreach($products as $product) {
            $productsData[] = $product->getSyncObjectData();
        }

        $data = [
            Product::getSyncArrayName() => $productsData
        ];

        $response = [
            self::SYNC_CHECKSUM => hash('sha256', json_encode($data)),
            self::SYNC_DATA     => $data
        ];

        return new JsonResponse($response, 200);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/vending_machines/{v_m_id}/nfc_tags",
     *      name = "sync_get_vending_machines_nfc_tags",
     *      host = "{domain_sync_v1}",
     *      defaults = { "_locale" = "%locale%", "domain_sync_v1" = "%domain_sync_v1%" },
     *      requirements = { "_locale" = "%locale%", "domain_sync_v1" = "%domain_sync_v1%", "v_m_id" = "\d+" }
     * )
     */
    public function getVendingMachinesNfcTagsAction(Request $request, $v_m_id)
    {

    }

    /**
     * @Method({"PUT"})
     * @Route(
     *      "/vending_machines/{v_m_id}",
     *      name = "sync_put_vending_machines",
     *      host = "{domain_sync_v1}",
     *      defaults = { "_locale" = "%locale%", "domain_sync_v1" = "%domain_sync_v1%" },
     *      requirements = { "_locale" = "%locale%", "domain_sync_v1" = "%domain_sync_v1%", "v_m_id" = "\d+" }
     * )
     */
    public function putVendingMachines(Request $request, $v_m_id)
    {
        $data = $request->request->get('request');
    }

    /**
     * @Method({"POST"})
     * @Route(
     *      "/vending_machines/{v_m_id}/purchases",
     *      name = "sync_post_vending_machines_purchases",
     *      host = "{domain_sync_v1}",
     *      defaults = { "_locale" = "%locale%", "domain_sync_v1" = "%domain_sync_v1%" },
     *      requirements = { "_locale" = "%locale%", "domain_sync_v1" = "%domain_sync_v1%", "v_m_id" = "\d+" }
     * )
     */
    public function postVendingMachinesPurchasesAction(Request $request, $v_m_id)
    {
        $data = $request->request->get('request');
    }

    /**
     * @Method({"POST"})
     * @Route(
     *      "/vending_machines/{v_m_id}/events",
     *      name = "sync_post_vending_machines_events",
     *      host = "{domain_sync_v1}",
     *      defaults = { "_locale" = "%locale%", "domain_sync_v1" = "%domain_sync_v1%" },
     *      requirements = { "_locale" = "%locale%", "domain_sync_v1" = "%domain_sync_v1%", "v_m_id" = "\d+" }
     * )
     */
    public function postVendingMachinesEvents(Request $request, $v_m_id)
    {
        $data = $request->request->get('request');
    }
}