<?php
// AppBundle/Controller/Sync/SyncController.php
namespace AppBundle\Controller\Sync;

use DateTime;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\JsonResponse,
    Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException,
    Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use AppBundle\Entity\VendingMachine\Utility\Interfaces\SyncVendingMachinePropertiesInterface,
    AppBundle\Entity\VendingMachine\Utility\Interfaces\SyncVendingMachineEventPropertiesInterface,
    AppBundle\Entity\Purchase\Utility\Interfaces\SyncPurchasePropertiesInterface;
use Symfony\Component\HttpFoundation\Response;

class SyncController extends Controller implements
    SyncVendingMachinePropertiesInterface,
    SyncVendingMachineEventPropertiesInterface,
    SyncPurchasePropertiesInterface
{
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
        $_manager = $this->getDoctrine()->getManager();

        $_authentication = $this->get('app.sync.security.authentication');

        $_syncDataBuilder = $this->get('app.sync.sync_data_builder');

        $_syncDataRecorder = $this->get('app.sync.sync_data_recorder');

        $vendingMachine = $_manager->getRepository('AppBundle:VendingMachine\VendingMachine')->findOneBy(['serial' => $serial]);

        if( !$vendingMachine )
            throw new NotFoundHttpException("Vending Machine identified by `id` {$serial} not found");

        if( !$_authentication->authenticate($request, $vendingMachine) )
            throw new AccessDeniedHttpException('Access denied');

        // exception on non-object!!!

        $products = $vendingMachine->getProductVendingGroup()->getProducts();

        $syncResponse = $_syncDataBuilder->buildProductData($products);

        $_syncDataRecorder->recordProductData($vendingMachine, $syncResponse);

        return new JsonResponse($syncResponse, 200);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/vending_machines/{serial}/nfc_tags",
     *      name = "sync_get_vending_machines_nfc_tags",
     *      host = "{domain_sync_v1}",
     *      defaults = { "_locale" = "%locale%", "domain_sync_v1" = "%domain_sync_v1%" },
     *      requirements = { "_locale" = "%locale%", "domain_sync_v1" = "%domain_sync_v1%" }
     * )
     */
    public function getVendingMachinesNfcTagsAction(Request $request, $serial)
    {
        $_manager = $this->getDoctrine()->getManager();

        $_authentication = $this->get('app.sync.security.authentication');

        $_syncDataBuilder = $this->get('app.sync.sync_data_builder');

        $_syncDataRecorder = $this->get('app.sync.sync_data_recorder');

        $vendingMachine = $_manager->getRepository('AppBundle:VendingMachine\VendingMachine')->findOneBy(['serial' => $serial]);

        if( !$vendingMachine )
            throw new NotFoundHttpException("Vending Machine identified by `id` {$serial} not found");

        if( !$_authentication->authenticate($request, $vendingMachine) )
            throw new AccessDeniedHttpException('Access denied');

        // exception on non-object!!!

        $nfcTags = $vendingMachine->getNfcTags();

        $syncResponse = $_syncDataBuilder->buildNfcTagData($nfcTags);

        $_syncDataRecorder->recordNfcTagData($vendingMachine, $syncResponse);

        return new JsonResponse($syncResponse, 200);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/vending_machines/{serial}/sync",
     *      name = "sync_get_vending_machines_sync",
     *      host = "{domain_sync_v1}",
     *      defaults = { "_locale" = "%locale%", "domain_sync_v1" = "%domain_sync_v1%" },
     *      requirements = { "_locale" = "%locale%", "domain_sync_v1" = "%domain_sync_v1%" }
     * )
     */
    public function getVendingMachinesSync(Request $request, $serial)
    {
        $_manager = $this->getDoctrine()->getManager();

        $_authentication = $this->get('app.sync.security.authentication');

        $_syncDataBuilder = $this->get('app.sync.sync_data_builder');

        $_syncDataRecorder = $this->get('app.sync.sync_data_recorder');

        $_syncDataValidator = $this->get('app.sync.sync_data_validator');

        $vendingMachine = $_manager->getRepository('AppBundle:VendingMachine\VendingMachine')->findOneBy(['serial' => $serial]);

        if( !$vendingMachine )
            throw new NotFoundHttpException("Vending Machine identified by `id` {$serial} not found");

        if( !$_authentication->authenticate($request, $vendingMachine) )
            throw new AccessDeniedHttpException('Access denied');

        // Validator of request?

        if( !$_syncDataValidator->validateVendingMachineSyncData($request) )
            return new Response(NULL, 400);


        $vendingMachineSync = $_manager->getRepository('AppBundle:VendingMachine\VendingMachineSync')->findLatestByVendingMachineSyncId($request->query->get('type'));

        $syncResponse = $_syncDataBuilder->buildSyncData($vendingMachineSync);

        $_syncDataRecorder->recordSyncData($vendingMachine, $syncResponse);

        return new JsonResponse($syncResponse, 200);
    }

    /**
     * @Method({"PUT"})
     * @Route(
     *      "/vending_machines/{serial}",
     *      name = "sync_put_vending_machines",
     *      host = "{domain_sync_v1}",
     *      defaults = { "_locale" = "%locale%", "domain_sync_v1" = "%domain_sync_v1%" },
     *      requirements = { "_locale" = "%locale%", "domain_sync_v1" = "%domain_sync_v1%" }
     * )
     */
    public function putVendingMachines(Request $request, $serial)
    {
        $_manager = $this->getDoctrine()->getManager();

        $_authentication = $this->get('app.sync.security.authentication');

        $_syncDataRecorder = $this->get('app.sync.sync_data_recorder');

        $_syncDataValidator = $this->get('app.sync.sync_data_validator');

        $vendingMachine = $_manager->getRepository('AppBundle:VendingMachine\VendingMachine')->findOneBy(['serial' => $serial]);

        if( !$vendingMachine )
            throw new NotFoundHttpException("Vending Machine identified by `id` {$serial} not found");

        if( !$_authentication->authenticate($request, $vendingMachine) )
            throw new AccessDeniedHttpException('Access denied');

        // Validator of request?

        if( !$_syncDataValidator->validateVendingMachineData($request) )
            return new Response(NULL, 400);

        $requestContent = json_decode($request->getContent(), TRUE);

        // check if exists

        $vendingMachineSync = $_manager->getRepository('AppBundle:VendingMachine\VendingMachineSync')->findOneBy([
            'vendingMachine'       => $vendingMachine,
            'vendingMachineSyncId' => $requestContent['data']['sync']['sync-id'],
            'syncedType'           => "..."
        ]);

        if( $vendingMachineSync )
            return new Response(NULL, 200);

        $vendingMachine->setVendingMachineLoadedAt(new DateTime($requestContent['data']['vending-machine']['load-datetime']));

        $_syncDataRecorder->recordVendingMachineData($vendingMachine, $requestContent);

        return new JsonResponse(NULL, 200);
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