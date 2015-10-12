<?php
// AppBundle/Controller/Sync/SyncController.php
namespace AppBundle\Controller\Sync;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\JsonResponse,
    Symfony\Component\HttpKernel\Exception\HttpException,
    Symfony\Component\HttpKernel\Exception\NotFoundHttpException,
    Symfony\Component\HttpKernel\Exception\BadRequestHttpException,
    Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Doctrine\DBAL\DBALException;

use AppBundle\Controller\Utility\Interfaces\Markers\SyncAuthenticationMarkerInterface,
    AppBundle\Entity\VendingMachine\Utility\Interfaces\SyncVendingMachineSyncPropertiesInterface;

class SyncController extends Controller implements
    SyncAuthenticationMarkerInterface,
    SyncVendingMachineSyncPropertiesInterface
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

        $vendingMachine = $_manager->getRepository('AppBundle:VendingMachine\VendingMachine')->findOneBy([
            'serial' => $serial
        ]);

        $_syncDataBuilder  = $this->get('app.sync.sync_data_builder');
        $_syncDataRecorder = $this->get('app.sync.sync_data_recorder');

        if( !($products = $vendingMachine->getProducts()) )
            throw new NotFoundHttpException('Vending Machine entity is missing required data');

        $syncResponse = $_syncDataBuilder->buildProductData($products);

        $_syncDataRecorder->recordProductData($vendingMachine, $syncResponse);

        return new JsonResponse($syncResponse);
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

        $vendingMachine = $_manager->getRepository('AppBundle:VendingMachine\VendingMachine')->findOneBy([
            'serial' => $serial
        ]);

        $_syncDataBuilder  = $this->get('app.sync.sync_data_builder');
        $_syncDataRecorder = $this->get('app.sync.sync_data_recorder');

        if( !($nfcTags = $vendingMachine->getNfcTags()) )
            throw new NotFoundHttpException('Vending Machine entity is missing required data');

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

        $vendingMachine = $_manager->getRepository('AppBundle:VendingMachine\VendingMachine')->findOneBy([
            'serial' => $serial
        ]);

        $_syncDataValidator = $this->get('app.sync.sync_data_validator');
        $_syncDataHandler   = $this->get('app.sync.sync_data_handler');
        $_syncDataBuilder   = $this->get('app.sync.sync_data_builder');
        $_syncDataRecorder  = $this->get('app.sync.sync_data_recorder');

        if( !($validSyncData = $_syncDataValidator->validateVendingMachineSyncData($request)) )
            throw new BadRequestHttpException('Request contains invalid data');

        $vendingMachineSyncData = $_syncDataHandler->handleVendingMachineSyncData($vendingMachine, $validSyncData);

        $syncResponse = $_syncDataBuilder->buildSyncData($vendingMachineSyncData);

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

        $vendingMachine = $_manager->getRepository('AppBundle:VendingMachine\VendingMachine')->findOneBy([
            'serial' => $serial
        ]);

        $_syncDataValidator = $this->get('app.sync.sync_data_validator');
        $_syncDataHandler   = $this->get('app.sync.sync_data_handler');
        $_syncDataRecorder  = $this->get('app.sync.sync_data_recorder');

        if( !($validSyncData = $_syncDataValidator->validateVendingMachineData($request)) )
            throw new BadRequestHttpException('Request contains invalid data');

        $_syncDataHandler->handleVendingMachineData($vendingMachine, $validSyncData);

        $_syncDataRecorder->recordVendingMachineData($vendingMachine, $validSyncData);

        return new JsonResponse(NULL, 200);
    }

    /**
     * @Method({"POST"})
     * @Route(
     *      "/vending_machines/{serial}/purchases",
     *      name = "sync_post_vending_machines_purchases",
     *      host = "{domain_sync_v1}",
     *      defaults = { "_locale" = "%locale%", "domain_sync_v1" = "%domain_sync_v1%" },
     *      requirements = { "_locale" = "%locale%", "domain_sync_v1" = "%domain_sync_v1%" }
     * )
     */
    public function postVendingMachinesPurchasesAction(Request $request, $serial)
    {
        $_manager = $this->getDoctrine()->getManager();

        $vendingMachine = $_manager->getRepository('AppBundle:VendingMachine\VendingMachine')->findOneBy([
            'serial' => $serial
        ]);

        $_syncDataValidator = $this->get('app.sync.sync_data_validator');
        $_syncDataHandler   = $this->get('app.sync.sync_data_handler');
        $_syncDataRecorder  = $this->get('app.sync.sync_data_recorder');

        if( !($validSyncData = $_syncDataValidator->validatePurchaseData($request)) )
            throw new BadRequestHttpException('Request contains invalid data');

        if( $_syncDataHandler->validateSyncSequence($vendingMachine, self::VENDING_MACHINE_SYNC_TYPE_PURCHASES, $validSyncData) )
            return new Response('Already in sync', 200);

        $_manager->getConnection()->beginTransaction();

        try {
            $_syncDataHandler->handlePurchaseData($vendingMachine, $validSyncData);

            $_syncDataRecorder->recordPurchaseData($vendingMachine, $validSyncData);

            $_manager->flush();
            $_manager->commit();
        } catch(DBALException $ex) {
            $_manager->getConnection()->rollback();
            throw new HttpException(500, 'Database transaction failed');
        }

        return new JsonResponse(NULL, 200);
    }

    /**
     * @Method({"POST"})
     * @Route(
     *      "/vending_machines/{serial}/events",
     *      name = "sync_post_vending_machines_events",
     *      host = "{domain_sync_v1}",
     *      defaults = { "_locale" = "%locale%", "domain_sync_v1" = "%domain_sync_v1%" },
     *      requirements = { "_locale" = "%locale%", "domain_sync_v1" = "%domain_sync_v1%" }
     * )
     */
    public function postVendingMachinesEvents(Request $request, $serial)
    {
        $_manager = $this->getDoctrine()->getManager();

        $vendingMachine = $_manager->getRepository('AppBundle:VendingMachine\VendingMachine')->findOneBy([
            'serial' => $serial
        ]);

        $_syncDataValidator = $this->get('app.sync.sync_data_validator');
        $_syncDataHandler   = $this->get('app.sync.sync_data_handler');
        $_syncDataRecorder  = $this->get('app.sync.sync_data_recorder');

        if( !($validSyncData = $_syncDataValidator->validateEventData($request)) )
            throw new BadRequestHttpException('Request contains invalid data');

        $_manager->getConnection()->beginTransaction();

        try {
            $_syncDataHandler->handleVendingMachineEventData($vendingMachine, $validSyncData);

            $_syncDataRecorder->recordVendingMachineEventData($vendingMachine, $validSyncData);

            $_manager->flush();

            $_manager->commit();
        } catch(DBALException $ex) {
            $_manager->getConnection()->rollback();
            throw new HttpException(500, 'Database transaction failed');
        }

        return new JsonResponse(NULL, 200);
    }
}