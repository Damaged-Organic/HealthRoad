<?php
// AppBundle/Controller/Sync/SyncController.php
namespace AppBundle\Controller\Sync;

use Exception;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use AppBundle\Controller\Utility\Traits\EntityFilter,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\JsonResponse,
    Symfony\Component\HttpKernel\Exception\NotFoundHttpException,
    Symfony\Component\HttpKernel\Exception\BadRequestHttpException,
    Symfony\Component\HttpKernel\Exception\FatalErrorException,
    Symfony\Component\Security\Core\Exception\BadCredentialsException,
    Symfony\Bundle\FrameworkBundle\Controller\Controller;

use JMS\DiExtraBundle\Annotation as DI;

use AppBundle\Controller\Utility\Interfaces\Markers\SyncAuthenticationMarkerInterface,
    AppBundle\Controller\Utility\Interfaces\Markers\SyncLoggingMarkerInterface,
    AppBundle\Entity\VendingMachine\Utility\Interfaces\SyncVendingMachineSyncPropertiesInterface,
    AppBundle\Event\PostVendingMachinesPurchasesEvent;

class SyncController extends Controller implements
    SyncAuthenticationMarkerInterface,
    SyncLoggingMarkerInterface,
    SyncVendingMachineSyncPropertiesInterface
{
    use EntityFilter;

    /** @DI\Inject("event_dispatcher") */
    protected $_dispatcher;

    /** @DI\Inject("doctrine.orm.entity_manager") */
    private $_manager;

    /** @DI\Inject("app.sync.sync_data_validator") */
    private $_syncDataValidator;

    /** @DI\Inject("app.sync.sync_data_handler") */
    private $_syncDataHandler;

    /** @DI\Inject("app.sync.sync_data_builder") */
    private $_syncDataBuilder;

    /** @DI\Inject("app.sync.sync_data_recorder") */
    private $_syncDataRecorder;

    /**
     * @Method({"GET"})
     * @Route(
     *      "/vending_machines/{serial}/sync",
     *      name = "sync_get_vending_machines_sync",
     *      host = "{domain_sync_v1}",
     *      schemes = {"http"},
     *      defaults = { "_locale" = "%locale%", "domain_sync_v1" = "%domain_sync_v1%" },
     *      requirements = { "_locale" = "%locale%", "domain_sync_v1" = "%domain_sync_v1%" }
     * )
     */
    public function getVendingMachinesSync(Request $request, $serial)
    {
        // $vendingMachine = $this->_manager->getRepository('AppBundle:VendingMachine\VendingMachine')->findOneBy([
        //     'serial' => $serial
        // ]);
        $vendingMachine = $this->_manager->getRepository('AppBundle:VendingMachine\VendingMachine')
            ->findOneBySerialPrefetchRelated($serial);

        if( !($validSyncData = $this->_syncDataValidator->validateVendingMachineSyncData($request)) )
            throw new BadRequestHttpException('Request contains invalid data');

        $vendingMachineSyncData = $this->_syncDataHandler->handleVendingMachineSyncData($vendingMachine, $validSyncData);

        $syncResponse = $this->_syncDataBuilder->buildSyncData($vendingMachineSyncData);

        $recordMethod = [$this->_syncDataRecorder, 'recordSyncData'];

        if( !$this->_syncDataRecorder->recordDataIfValid($vendingMachine, $syncResponse, $recordMethod) )
            throw new BadCredentialsException('Sync response array is missing required data');

        //return new JsonResponse($syncResponse, 200);
        return new Response(json_encode($syncResponse, JSON_UNESCAPED_UNICODE), 200);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/vending_machines/{serial}/products",
     *      name = "sync_get_vending_machines_products",
     *      host = "{domain_sync_v1}",
     *      schemes = {"http"},
     *      defaults = { "_locale" = "%locale%", "domain_sync_v1" = "%domain_sync_v1%" },
     *      requirements = { "_locale" = "%locale%", "domain_sync_v1" = "%domain_sync_v1%" }
     * )
     */
    public function getProductsAction($serial)
    {
        // $vendingMachine = $this->_manager->getRepository('AppBundle:VendingMachine\VendingMachine')->findOneBy([
        //     'serial' => $serial
        // ]);
        $vendingMachine = $this->_manager->getRepository('AppBundle:VendingMachine\VendingMachine')
            ->findOneBySerialPrefetchRelated($serial);

        $products = $this->filterDeleted($vendingMachine->getProducts());

        $syncResponse = $this->_syncDataBuilder->buildProductData($products);

        $recordMethod = [$this->_syncDataRecorder, 'recordProductData'];

        if( !$this->_syncDataRecorder->recordDataIfValid($vendingMachine, $syncResponse, $recordMethod) )
            throw new BadCredentialsException('Sync response array is missing required data');

        //return new JsonResponse($syncResponse);
        return new Response(json_encode($syncResponse, JSON_UNESCAPED_UNICODE), 200);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/vending_machines/{serial}/nfc_tags",
     *      name = "sync_get_vending_machines_nfc_tags",
     *      host = "{domain_sync_v1}",
     *      schemes = {"http"},
     *      defaults = { "_locale" = "%locale%", "domain_sync_v1" = "%domain_sync_v1%" },
     *      requirements = { "_locale" = "%locale%", "domain_sync_v1" = "%domain_sync_v1%" }
     * )
     */
    public function getVendingMachinesNfcTagsAction($serial)
    {
        // $vendingMachine = $this->_manager->getRepository('AppBundle:VendingMachine\VendingMachine')->findOneBy([
        //     'serial' => $serial
        // ]);
        $vendingMachine = $this->_manager->getRepository('AppBundle:VendingMachine\VendingMachine')
            ->findOneBySerialPrefetchRelated($serial);

        $students = $this->filterDeleted($vendingMachine->getStudents());

        $syncResponse = $this->_syncDataBuilder->buildNfcTagData($students);

        $recordMethod = [$this->_syncDataRecorder, 'recordNfcTagData'];

        if( !$this->_syncDataRecorder->recordDataIfValid($vendingMachine, $syncResponse, $recordMethod) )
            throw new BadCredentialsException('Sync response array is missing required data');

        // return new JsonResponse($syncResponse, 200);
        return new Response(json_encode($syncResponse, JSON_UNESCAPED_UNICODE), 200);
    }

    /**
     * @Method({"PUT"})
     * @Route(
     *      "/vending_machines/{serial}",
     *      name = "sync_put_vending_machines",
     *      host = "{domain_sync_v1}",
     *      schemes = {"http"},
     *      defaults = { "_locale" = "%locale%", "domain_sync_v1" = "%domain_sync_v1%" },
     *      requirements = { "_locale" = "%locale%", "domain_sync_v1" = "%domain_sync_v1%" }
     * )
     */
    public function putVendingMachines(Request $request, $serial)
    {
        // $vendingMachine = $this->_manager->getRepository('AppBundle:VendingMachine\VendingMachine')->findOneBy([
        //     'serial' => $serial
        // ]);
        $vendingMachine = $this->_manager->getRepository('AppBundle:VendingMachine\VendingMachine')
            ->findOneBySerialPrefetchRelated($serial);

        if( !($validSyncData = $this->_syncDataValidator->validateVendingMachineData($request)) )
            throw new BadRequestHttpException('Request contains invalid data');

        $this->_manager->transactional(function($_manager) use($validSyncData, $vendingMachine)
        {
            $this->_syncDataHandler->handleVendingMachineData($vendingMachine, $validSyncData);

            $recordMethod = [$this->_syncDataRecorder, 'recordVendingMachineData'];

            if (!$this->_syncDataRecorder->recordDataIfValid($vendingMachine, $validSyncData, $recordMethod))
                throw new BadCredentialsException('Sync response array is missing required data');

            $_manager->flush();
        });

        // return new JsonResponse(NULL, 200);
        return new Response(json_encode(NULL, JSON_UNESCAPED_UNICODE), 200);
    }

    /**
     * @Method({"POST"})
     * @Route(
     *      "/vending_machines/{serial}/purchases",
     *      name = "sync_post_vending_machines_purchases",
     *      host = "{domain_sync_v1}",
     *      schemes = {"http"},
     *      defaults = { "_locale" = "%locale%", "domain_sync_v1" = "%domain_sync_v1%" },
     *      requirements = { "_locale" = "%locale%", "domain_sync_v1" = "%domain_sync_v1%" }
     * )
     */
    public function postVendingMachinesPurchasesAction(Request $request, $serial)
    {
        // $vendingMachine = $this->_manager->getRepository('AppBundle:VendingMachine\VendingMachine')->findOneBy([
        //     'serial' => $serial
        // ]);
        $vendingMachine = $this->_manager->getRepository('AppBundle:VendingMachine\VendingMachine')
            ->findOneBySerialPrefetchRelated($serial);

        if( !($validSyncData = $this->_syncDataValidator->validatePurchaseData($request)) )
            throw new BadRequestHttpException('Request contains invalid data');

        if( $this->_syncDataValidator->validateSyncSequence($vendingMachine, self::VENDING_MACHINE_SYNC_TYPE_PURCHASES, $validSyncData) )
            return new Response('Already in sync', 200);

        $this->_manager->getConnection()->beginTransaction();

        try{
            $vendingMachineSyncId = $this->_syncDataHandler->handlePurchaseData($vendingMachine, $validSyncData);

            $recordMethod = [$this->_syncDataRecorder, 'recordPurchaseData'];

            if( !$this->_syncDataRecorder->recordDataIfValid($vendingMachine, $validSyncData, $recordMethod) )
                throw new BadCredentialsException('Sync response array is missing required data');

            $this->_manager->flush();
            $this->_manager->clear();

            $this->_manager->getConnection()->commit();
        }catch( Exception $e ){
            $this->_manager->getConnection()->rollback();

            throw new FatalErrorException('Database Error');
        }

        // Send notifications
        $postVendingMachinesPurchasesEvent = new PostVendingMachinesPurchasesEvent($vendingMachine, $vendingMachineSyncId);
        $this->_dispatcher->dispatch('app.event.post_vending_machines_purchases.after', $postVendingMachinesPurchasesEvent);

        // return new JsonResponse(NULL, 200);
        return new Response(json_encode(NULL, JSON_UNESCAPED_UNICODE), 200);
    }

    /**
     * @Method({"POST"})
     * @Route(
     *      "/vending_machines/{serial}/transactions",
     *      name = "sync_post_vending_machines_transactions",
     *      host = "{domain_sync_v1}",
     *      schemes = {"http"},
     *      defaults = { "_locale" = "%locale%", "domain_sync_v1" = "%domain_sync_v1%" },
     *      requirements = { "_locale" = "%locale%", "domain_sync_v1" = "%domain_sync_v1%" }
     * )
     */
    public function postVendingMachinesTransactions(Request $request, $serial)
    {
        $vendingMachine = $this->_manager->getRepository('AppBundle:VendingMachine\VendingMachine')
            ->findOneBySerialPrefetchRelated($serial);

        if( !($validSyncData = $this->_syncDataValidator->validateTransactionData($request)) )
            throw new BadRequestHttpException('Request contains invalid data');

        if( $this->_syncDataValidator->validateSyncSequence($vendingMachine, self::VENDING_MACHINE_SYNC_TYPE_TRANSACTIONS, $validSyncData) )
            return new Response('Already in sync', 200);

        $this->_manager->getConnection()->beginTransaction();

        try{
            $vendingMachineSyncId = $this->_syncDataHandler->handleTransactionData($vendingMachine, $validSyncData);

            // $recordMethod = [$this->_syncDataRecorder, 'recordTransactionData'];
            //
            // if( !$this->_syncDataRecorder->recordDataIfValid($vendingMachine, $validSyncData, $recordMethod) )
            //     throw new BadCredentialsException('Sync response array is missing required data');

            $this->_manager->flush();
            $this->_manager->clear();

            $this->_manager->getConnection()->commit();
        }catch( Exception $e ){
            $this->_manager->getConnection()->rollback();

            throw new FatalErrorException('Database Error: ' . $e->getMessage());
        }

        return new Response(json_encode(NULL, JSON_UNESCAPED_UNICODE), 200);
    }

    /**
     * @Method({"POST"})
     * @Route(
     *      "/vending_machines/{serial}/events",
     *      name = "sync_post_vending_machines_events",
     *      host = "{domain_sync_v1}",
     *      schemes = {"http"},
     *      defaults = { "_locale" = "%locale%", "domain_sync_v1" = "%domain_sync_v1%" },
     *      requirements = { "_locale" = "%locale%", "domain_sync_v1" = "%domain_sync_v1%" }
     * )
     */
    public function postVendingMachinesEvents(Request $request, $serial)
    {
        // $vendingMachine = $this->_manager->getRepository('AppBundle:VendingMachine\VendingMachine')->findOneBy([
        //     'serial' => $serial
        // ]);
        $vendingMachine = $this->_manager->getRepository('AppBundle:VendingMachine\VendingMachine')
            ->findOneBySerialPrefetchRelated($serial);

        if( !($validSyncData = $this->_syncDataValidator->validateEventData($request)) )
            throw new BadRequestHttpException('Request contains invalid data');

        $this->_manager->transactional(function($_manager) use($validSyncData, $vendingMachine)
        {
            $this->_syncDataHandler->handleVendingMachineEventData($vendingMachine, $validSyncData);

            $recordMethod = [$this->_syncDataRecorder, 'recordVendingMachineEventData'];

            if( !$this->_syncDataRecorder->recordDataIfValid($vendingMachine, $validSyncData, $recordMethod) )
                throw new BadCredentialsException('Sync response array is missing required data');

            $_manager->flush();
        });

        // return new JsonResponse(NULL, 200);
        return new Response(json_encode(NULL, JSON_UNESCAPED_UNICODE), 200);
    }
}
