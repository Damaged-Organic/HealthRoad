<?php
// AppBundle/Controller/Sync/SyncController.php
namespace AppBundle\Controller\Sync;

use AppBundle\Entity\Purchase\Purchase;
use DateTime;

use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\JsonResponse,
    Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException,
    Symfony\Component\HttpKernel\Exception\NotFoundHttpException,
    Symfony\Component\HttpKernel\Exception\BadRequestHttpException,
    Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\VendingMachine\Utility\Interfaces\SyncVendingMachineSyncPropertiesInterface,
    AppBundle\Entity\VendingMachine\Utility\Interfaces\SyncVendingMachineEventPropertiesInterface,
    AppBundle\Entity\Purchase\Utility\Interfaces\SyncPurchasePropertiesInterface;

class SyncController extends Controller implements
    SyncVendingMachineSyncPropertiesInterface,
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
        // Log request here!

        if( !($vendingMachine = $this->getVendingMachineIfRequestIsValid($request, $serial)) )
            throw new AccessDeniedHttpException('Access denied');

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
        // Log request here!

        if( !($vendingMachine = $this->getVendingMachineIfRequestIsValid($request, $serial)) )
            throw new AccessDeniedHttpException('Access denied');

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
        // Log request here!

        if( !($vendingMachine = $this->getVendingMachineIfRequestIsValid($request, $serial)) )
            throw new AccessDeniedHttpException('Access denied');

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
        if( !($vendingMachine = $this->getVendingMachineIfRequestIsValid($request, $serial)) )
            throw new AccessDeniedHttpException('Access denied');

        $_syncDataValidator = $this->get('app.sync.sync_data_validator');
        $_syncDataHandler   = $this->get('app.sync.sync_data_handler');
        $_syncDataRecorder  = $this->get('app.sync.sync_data_recorder');

        if( !($validSyncData = $_syncDataValidator->validateVendingMachineData($request)) )
            throw new BadRequestHttpException('Request contains invalid data');

        /*if( $_syncDataHandler->validateSyncSequence($vendingMachine, self::VENDING_MACHINE_SYNC_TYPE_VENDING_MACHINE, $validSyncData) )
            return new Response('Already in sync', 200);*/

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
        if( !($vendingMachine = $this->getVendingMachineIfRequestIsValid($request, $serial)) )
            throw new AccessDeniedHttpException('Access denied');

        $_syncDataValidator = $this->get('app.sync.sync_data_validator');
        $_syncDataHandler   = $this->get('app.sync.sync_data_handler');
        $_syncDataRecorder  = $this->get('app.sync.sync_data_recorder');

        if( !($validSyncData = $_syncDataValidator->validatePurchaseData($request)) )
            throw new BadRequestHttpException('Request contains invalid data');

        if( $_syncDataHandler->validateSyncSequence($vendingMachine, self::VENDING_MACHINE_SYNC_TYPE_PURCHASES, $validSyncData) )
            return new Response('Already in sync', 200);

        /*$doctrine = $this->getDoctrine();
        $em = $doctrine->getConnection();
        $stack = new \Doctrine\DBAL\Logging\DebugStack();
        $em->getConfiguration()->setSQLLogger($stack);*/

        $this->getDoctrine()->getManager()->getConnection()->beginTransaction();

        try {
            $_syncDataHandler->handlePurchaseData($vendingMachine, $validSyncData);

            $_syncDataRecorder->recordPurchaseData($vendingMachine, $validSyncData);

            $this->getDoctrine()->getManager()->flush();

            $this->getDoctrine()->getManager()->commit();
        } catch (\Exception $e) {
            $this->getDoctrine()->getManager()->getConnection()->rollback();
            throw $e;
        }

        /*echo "<pre>";
        var_dump($stack->queries);
        echo "</pre>";*/

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
        if( !($vendingMachine = $this->getVendingMachineIfRequestIsValid($request, $serial)) )
            throw new AccessDeniedHttpException('Access denied');

        $_syncDataValidator = $this->get('app.sync.sync_data_validator');
        $_syncDataHandler   = $this->get('app.sync.sync_data_handler');
        $_syncDataRecorder  = $this->get('app.sync.sync_data_recorder');

        if( !($validSyncData = $_syncDataValidator->validateEventData($request)) )
            throw new BadRequestHttpException('Request contains invalid data');

        $this->getDoctrine()->getManager()->getConnection()->beginTransaction();

        try {
            $_syncDataHandler->handleVendingMachineEventData($vendingMachine, $validSyncData);

            $_syncDataRecorder->recordVendingMachineEventData($vendingMachine, $validSyncData);

            $this->getDoctrine()->getManager()->flush();

            $this->getDoctrine()->getManager()->commit();
        } catch (\Exception $e) {
            $this->getDoctrine()->getManager()->getConnection()->rollback();
            throw $e;
        }

        return new JsonResponse(NULL, 200);
    }

    private function getVendingMachineIfRequestIsValid(Request $request, $serial)
    {
        $_manager = $this->getDoctrine()->getManager();

        $_authentication = $this->get('app.sync.security.authentication');

        $vendingMachine = $_manager->getRepository('AppBundle:VendingMachine\VendingMachine')->findOneBy(['serial' => $serial]);

        if( !$vendingMachine )
            return FALSE;

        if( !$_authentication->authenticate($request, $vendingMachine) )
            return FALSE;

        return $vendingMachine;
    }
}