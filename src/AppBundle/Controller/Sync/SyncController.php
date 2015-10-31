<?php
// AppBundle/Controller/Sync/SyncController.php
namespace AppBundle\Controller\Sync;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\JsonResponse,
    Symfony\Component\HttpKernel\Exception\NotFoundHttpException,
    Symfony\Component\HttpKernel\Exception\BadRequestHttpException,
    Symfony\Component\Security\Core\Exception\BadCredentialsException,
    Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Controller\Utility\Interfaces\Markers\SyncAuthenticationMarkerInterface,
    AppBundle\Entity\VendingMachine\Utility\Interfaces\SyncVendingMachineSyncPropertiesInterface;

class SyncController extends Controller implements
    SyncAuthenticationMarkerInterface,
    SyncVendingMachineSyncPropertiesInterface
{
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

        if( !$vendingMachineSyncData )
            throw new NotFoundHttpException('Vending Machine has no synchronization of a given type');

        $syncResponse = $_syncDataBuilder->buildSyncData($vendingMachineSyncData);

        $recordMethod = [$_syncDataRecorder, 'recordSyncData'];

        if( !$_syncDataRecorder->recordDataIfValid($vendingMachine, $syncResponse, $recordMethod) )
            throw new BadCredentialsException('Sync response array is missing required data');

        return new JsonResponse($syncResponse, 200);
    }

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
    public function getProductsAction($serial)
    {
        $_manager = $this->getDoctrine()->getManager();

        $vendingMachine = $_manager->getRepository('AppBundle:VendingMachine\VendingMachine')->findOneBy([
            'serial' => $serial
        ]);

        $_syncDataBuilder  = $this->get('app.sync.sync_data_builder');
        $_syncDataRecorder = $this->get('app.sync.sync_data_recorder');

        if( !($products = $vendingMachine->getProducts()) )
            throw new NotFoundHttpException('Vending Machine has no linked products');

        $syncResponse = $_syncDataBuilder->buildProductData($products);

        $recordMethod = [$_syncDataRecorder, 'recordProductData'];

        if( !$_syncDataRecorder->recordDataIfValid($vendingMachine, $syncResponse, $recordMethod) )
            throw new BadCredentialsException('Sync response array is missing required data');

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
    public function getVendingMachinesNfcTagsAction($serial)
    {
        $_manager = $this->getDoctrine()->getManager();

        $vendingMachine = $_manager->getRepository('AppBundle:VendingMachine\VendingMachine')->findOneBy([
            'serial' => $serial
        ]);

        $_syncDataBuilder  = $this->get('app.sync.sync_data_builder');
        $_syncDataRecorder = $this->get('app.sync.sync_data_recorder');

        if( !($students = $vendingMachine->getStudents()) )
            throw new NotFoundHttpException('Vending Machine has no linked NFC tags');

        $syncResponse = $_syncDataBuilder->buildNfcTagData($students);

        $recordMethod = [$_syncDataRecorder, 'recordNfcTagData'];

        if( !$_syncDataRecorder->recordDataIfValid($vendingMachine, $syncResponse, $recordMethod) )
            throw new BadCredentialsException('Sync response array is missing required data');

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

        $recordMethod = [$_syncDataRecorder, 'recordVendingMachineData'];

        if( !$_syncDataRecorder->recordDataIfValid($vendingMachine, $validSyncData, $recordMethod) )
            throw new BadCredentialsException('Sync response array is missing required data');

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

        if( $_syncDataValidator->validateSyncSequence($vendingMachine, self::VENDING_MACHINE_SYNC_TYPE_PURCHASES, $validSyncData) )
            return new Response('Already in sync', 200);

        $_manager->transactional(function($_manager) use($_syncDataHandler, $_syncDataRecorder, $validSyncData, $vendingMachine)
        {
            $_syncDataHandler->handlePurchaseData($vendingMachine, $validSyncData);

            $recordMethod = [$_syncDataRecorder, 'recordPurchaseData'];

            if( !$_syncDataRecorder->recordDataIfValid($vendingMachine, $validSyncData, $recordMethod) )
                throw new BadCredentialsException('Sync response array is missing required data');

            $_manager->flush();
        });

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

        $_manager->transactional(function($_manager) use($_syncDataHandler, $_syncDataRecorder, $validSyncData, $vendingMachine)
        {
            $_syncDataHandler->handleVendingMachineEventData($vendingMachine, $validSyncData);

            $recordMethod = [$_syncDataRecorder, 'recordVendingMachineEventData'];

            if( !$_syncDataRecorder->recordDataIfValid($vendingMachine, $validSyncData, $recordMethod) )
                throw new BadCredentialsException('Sync response array is missing required data');

            $_manager->flush();
        });

        return new JsonResponse(NULL, 200);
    }
}