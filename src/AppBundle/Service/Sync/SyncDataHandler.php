<?php
// AppBundle/Service/Sync/SyncDataHandler.php
namespace AppBundle\Service\Sync;

use DateTime;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;

use Psr\Log\LoggerInterface;

use AppBundle\Service\Sync\Utility\Interfaces\SyncDataInterface,
    AppBundle\Entity\VendingMachine\Utility\Interfaces\SyncVendingMachinePropertiesInterface,
    AppBundle\Entity\VendingMachine\Utility\Interfaces\SyncVendingMachineSyncPropertiesInterface,
    AppBundle\Entity\VendingMachine\Utility\Interfaces\SyncVendingMachineLoadPropertiesInterface,
    AppBundle\Entity\VendingMachine\VendingMachine,
    AppBundle\Entity\VendingMachine\VendingMachineSync,
    AppBundle\Entity\VendingMachine\VendingMachineEvent,
    AppBundle\Entity\VendingMachine\VendingMachineLoad,
    AppBundle\Entity\Purchase\Purchase;

class SyncDataHandler implements
    SyncDataInterface,
    SyncVendingMachinePropertiesInterface,
    SyncVendingMachineSyncPropertiesInterface,
    SyncVendingMachineLoadPropertiesInterface
{
    private $_manager;
    private $_logger;

    public function setManager(EntityManager $manager)
    {
        $this->_manager = $manager;
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->_logger = $logger;
    }

    public function handleVendingMachineSyncData($vendingMachine, $data)
    {
        $vendingMachineSync = $this->_manager->getRepository('AppBundle:VendingMachine\VendingMachineSync')
            ->findLatestByVendingMachineSyncType($vendingMachine, $data[self::VENDING_MACHINE_SYNC_TYPE]);

        return $vendingMachineSync;
    }

    public function handleVendingMachineData($vendingMachine, $data)
    {
        $vendingMachine->setVendingMachineLoadedAt(new DateTime($data[self::SYNC_DATA][self::VENDING_MACHINE_ARRAY][0][self::VENDING_MACHINE_LOAD_LOADED_AT]));

        $vendingMachineLoadArray = [];

        foreach( $data[self::SYNC_DATA][self::VENDING_MACHINE_LOAD_ARRAY] as $value )
        {
            $vendingMachineLoad = (new VendingMachineLoad)
                ->setVendingMachine($vendingMachine)
                ->setProductId($value[VendingMachineLoad::VENDING_MACHINE_LOAD_PRODUCT_ID])
                ->setLoadedAt($value[VendingMachineLoad::VENDING_MACHINE_LOAD_DATETIME])
                ->setProductQuantity($value[VendingMachineLoad::VENDING_MACHINE_LOAD_PRODUCT_QUANTITY])
                ->setSpringPosition($value[VendingMachineLoad::VENDING_MACHINE_LOAD_SPRING_POSITION])
            ;

            $vendingMachineLoadArray[] = $vendingMachineLoad;
        }

        if( $vendingMachineLoadArray )
        {
            $this->_manager->getRepository('AppBundle:VendingMachine\VendingMachineLoad')->rawDeleteVendingMachineLoad($vendingMachine);

            $this->_manager->getRepository('AppBundle:VendingMachine\VendingMachineLoad')->rawInsertVendingMachineLoad($vendingMachineLoadArray);
        }

        $this->_manager->persist($vendingMachine);
    }

    public function handlePurchaseData(VendingMachine $vendingMachine, $data)
    {
        // TODO: This fallback will cause problems, if prices in ProductVendingGroup are different from default
        if( !($products = $vendingMachine->getProducts()) ) {
            // Fallback to all available products, could signal problem
            $products = new ArrayCollection($this->_manager->getRepository('AppBundle:Product\Product')->findAll());
        }

        if( !($nfcTags = new ArrayCollection($this->_manager->getRepository('AppBundle:NfcTag\NfcTag')->findAvailableByVendingMachine($vendingMachine))) ) {
            // Fallback to all available NFC tags, could signal problem
            $nfcTags = new ArrayCollection($this->_manager->getRepository('AppBundle:NfcTag\NfcTag')->findAllIndexedByCode());
        }

        $purchasesArray = [];

        foreach( $data[self::SYNC_DATA][Purchase::getSyncArrayName()] as $value )
        {
            // KLUDGE: set code to lower case (minor TA architecture failure)
            $value[Purchase::PURCHASE_NFC_CODE] = mb_strtolower($value[Purchase::PURCHASE_NFC_CODE], 'UTF-8');

            if( $nfcTags->get($value[Purchase::PURCHASE_NFC_CODE]) && $products->get($value[Purchase::PURCHASE_PRODUCT_ID]))
            {
                $purchase = (new Purchase)
                    ->setSyncPurchaseId($value[Purchase::PURCHASE_SYNC_ID])
                    ->setSyncPurchasedAt(new DateTime($value[Purchase::PURCHASE_PURCHASED_AT]));

                $purchase
                    ->setVendingMachine($vendingMachine)
                    ->setVendingMachineSerial($vendingMachine->getSerial())
                    ->setVendingMachineSyncId($data[self::SYNC_DATA][VendingMachineSync::getSyncArrayName()][0][self::VENDING_MACHINE_SYNC_ID]);

                $purchase
                    ->setSyncProductId($value[Purchase::PURCHASE_PRODUCT_ID])
                    ->setSyncProductPrice($value[Purchase::PURCHASE_SYNC_PRODUCT_PRICE])
                    ->setProduct(
                        ($products->get($value[Purchase::PURCHASE_PRODUCT_ID])) ? $products->get($value[Purchase::PURCHASE_PRODUCT_ID]) : NULL
                    );

                $purchase
                    ->setSyncNfcTagCode($value[Purchase::PURCHASE_NFC_CODE])
                    ->setNfcTag(
                        ($nfcTags->get($value[Purchase::PURCHASE_NFC_CODE])) ? $nfcTags->get($value[Purchase::PURCHASE_NFC_CODE]) : NULL
                    );

                // TRICKY: Setting NFC Tag and Student separately, to preserve purchase history
                // in case if persisted NFC Tag is [unbinded from / binded to other] Student
                $purchase
                    ->setStudent(
                        ($nfcTags->get($value[Purchase::PURCHASE_NFC_CODE]))
                            ? (( $nfcTags->get($value[Purchase::PURCHASE_NFC_CODE])->getStudent() ) ? $nfcTags->get($value[Purchase::PURCHASE_NFC_CODE])->getStudent() : NULL)
                            : NULL
                    )
                ;

                $purchasesArray[] = $purchase;
            } else {
                //Logging value that somehow (!) contains wrong bindings
                $this->_logger->warning("SyncDataHandler: VM `" . $vendingMachine->getSerial() . "` posted contradictory NfcTag `code` or Product `id`:" . json_encode($value));
            }
        }

        // When purchases empty?
        if( $purchasesArray )
        {
            $this->_manager->getRepository('AppBundle:Purchase\Purchase')->rawInsertPurchases($purchasesArray);

            $purchasesAggregated = $this->_manager->getRepository('AppBundle:Purchase\Purchase')->findSumsByStudentsWithSyncId(
                $vendingMachine,
                $data[self::SYNC_DATA][VendingMachineSync::getSyncArrayName()][0][self::VENDING_MACHINE_SYNC_ID]
            );

            $studentsArray = [];

            foreach( $purchasesAggregated as $purchase )
            {
                if( $nfcTags->get($purchase['code'])->getStudent() )
                {
                    $totalLimit = $nfcTags->get($purchase['code'])->getStudent()->getTotalLimit();

                    $totalLimit = bcsub($totalLimit, $purchase['price_sum'], 2);

                    $studentsArray[] = ['id' => $nfcTags->get($purchase['code'])->getStudent()->getId(), 'totalLimit' => $totalLimit];
                } else {
                    //Logging NfcTag that somehow (!) is not binded to Student
                    $this->_logger->warning("SyncDataHandler: VM `" . $vendingMachine->getSerial() . "` posted unbinded NfcTag `code`:" . $nfcTags->get($purchase['code'])->getCode());
                }
            }

            // When students empty?
            if( $studentsArray ) {
                $this->_manager->getRepository('AppBundle:Student\Student')->rawUpdateStudentsTotalLimits($studentsArray);
            }
        }

        return $data[self::SYNC_DATA][VendingMachineSync::getSyncArrayName()][0][self::VENDING_MACHINE_SYNC_ID];
    }

    public function handleVendingMachineEventData(VendingMachine $vendingMachine, $data)
    {
        $eventsArray = [];

        foreach( $data[self::SYNC_DATA][VendingMachineEvent::getSyncArrayName()] as $value )
        {
            $vendingMachineEvent = (new VendingMachineEvent)
                ->setSyncEventId($value[VendingMachineEvent::VENDING_MACHINE_EVENT_ID])
                ->setOccurredAt($value[VendingMachineEvent::VENDING_MACHINE_EVENT_DATETIME])
                ->setType($value[VendingMachineEvent::VENDING_MACHINE_EVENT_TYPE])
                ->setCode($value[VendingMachineEvent::VENDING_MACHINE_EVENT_CODE])
                ->setMessage($value[VendingMachineEvent::VENDING_MACHINE_EVENT_MESSAGE])
            ;

            $vendingMachineEvent
                ->setVendingMachine($vendingMachine)
            ;

            $eventsArray[] = $vendingMachineEvent;
        }

        // if events empty will except
        $this->_manager->getRepository('AppBundle:VendingMachine\VendingMachineEvent')->rawInsertVendingMachineEvents($eventsArray);
    }
}
