<?php
// AppBundle/Controller/Sync/SyncController.php
namespace AppBundle\Controller\Sync;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\Product\Utility\Interfaces\SyncProductPropertiesInterface,
    AppBundle\Entity\NfcTag\Utility\Interfaces\SyncNfcTagPropertiesInterface,
    AppBundle\Entity\VendingMachine\Utility\Interfaces\SyncVendingMachinePropertiesInterface,
    AppBundle\Entity\VendingMachine\Utility\Interfaces\SyncVendingMachineEventPropertiesInterface,
    AppBundle\Entity\Purchase\Utility\Interfaces\SyncPurchasePropertiesInterface;
use Symfony\Component\HttpFoundation\Response;

class SyncController extends Controller implements
    SyncProductPropertiesInterface,
    SyncNfcTagPropertiesInterface,
    SyncVendingMachinePropertiesInterface,
    SyncVendingMachineEventPropertiesInterface,
    SyncPurchasePropertiesInterface
{
    /**
     * @Method({"GET"})
     * @Route(
     *      "/vending_machines/{v_m_id}/products",
     *      name = "sync_get_vending_machines_products",
     *      host = "{domain_sync}",
     *      defaults = { "_locale" = "%locale%", "domain_sync" = "%domain_sync%" },
     *      requirements = { "_locale" = "%locale%", "domain_sync" = "%domain_sync%", "v_m_id" = "\d+" }
     * )
     */
    public function getProductsAction(Request $request, $v_m_id)
    {
        // Check authorization

        file_put_contents(
            'test.txt',
            print_r([
                'login'    => $request->query->get('login'),
                'password' => $request->query->get('password')
            ], TRUE),
            FILE_APPEND
        );

        // On a non-object error

        $_manager = $this->getDoctrine()->getManager();

        $vendingMachine = $_manager->getRepository('AppBundle:VendingMachine\VendingMachine')->find($v_m_id);

        $products = $vendingMachine->getProductVendingGroup()->getProducts();

        foreach($products as $product) {
            $productsData[] = [
                'id'    => $product->getId(),
                'name'  => $product->getNameShort(),
                'price' => $product->getPrice()
            ];
        }

        $productsDataOutput['data'] = [
            'products' => $productsData
        ];

        $productsDataOutput['checksum'] = hash('sha256', json_encode($productsData));

        $productsDataOutput = json_encode($productsDataOutput);

        return new Response($productsDataOutput, 200);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/vending_machines/{v_m_id}/nfc_tags",
     *      name = "sync_get_vending_machines_nfc_tags",
     *      host = "{domain_sync}",
     *      defaults = { "_locale" = "%locale%", "domain_sync" = "%domain_sync%" },
     *      requirements = { "_locale" = "%locale%", "domain_sync" = "%domain_sync%", "v_m_id" = "\d+" }
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
     *      host = "{domain_sync}",
     *      defaults = { "_locale" = "%locale%", "domain_sync" = "%domain_sync%" },
     *      requirements = { "_locale" = "%locale%", "domain_sync" = "%domain_sync%", "v_m_id" = "\d+" }
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
     *      host = "{domain_sync}",
     *      defaults = { "_locale" = "%locale%", "domain_sync" = "%domain_sync%" },
     *      requirements = { "_locale" = "%locale%", "domain_sync" = "%domain_sync%", "v_m_id" = "\d+" }
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
     *      host = "{domain_sync}",
     *      defaults = { "_locale" = "%locale%", "domain_sync" = "%domain_sync%" },
     *      requirements = { "_locale" = "%locale%", "domain_sync" = "%domain_sync%", "v_m_id" = "\d+" }
     * )
     */
    public function postVendingMachinesEvents(Request $request, $v_m_id)
    {
        $data = $request->request->get('request');
    }
}