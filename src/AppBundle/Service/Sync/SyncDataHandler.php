<?php
// AppBundle/Service/Sync/SyncDataHandler.php
namespace AppBundle\Service\Sync;

use AppBundle\Entity\Purchase\Purchase;
use AppBundle\Entity\VendingMachine\VendingMachine;
use AppBundle\Entity\VendingMachine\VendingMachineEvent;
use AppBundle\Entity\VendingMachine\VendingMachineSync;
use DateTime;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;

use AppBundle\Service\Sync\Utility\Interfaces\SyncDataInterface,
    AppBundle\Entity\VendingMachine\Utility\Interfaces\SyncVendingMachinePropertiesInterface,
    AppBundle\Entity\VendingMachine\Utility\Interfaces\SyncVendingMachineSyncPropertiesInterface;

class SyncDataHandler implements
    SyncDataInterface,
    SyncVendingMachinePropertiesInterface,
    SyncVendingMachineSyncPropertiesInterface
{
    private $_manager;

    public function setManager(EntityManager $manager)
    {
        $this->_manager = $manager;
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

        $this->_manager->persist($vendingMachine);
    }

    public function handlePurchaseData(VendingMachine $vendingMachine, $data)
    {
        //if products null?
        $products = $vendingMachine->getProducts();

        //if no students?
        $students = $vendingMachine->getStudents();

        $nfcTags = new ArrayCollection;

        //if students have no tags?
        foreach($students as $student) {
            $nfcTags->set($student->getNfcTag()->getCode(), $student->getNfcTag());
        }

        $purchasesArray = [];

        foreach( $data[self::SYNC_DATA][Purchase::getSyncArrayName()] as $value )
        {
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

                $purchasesArray[] = $purchase;
            }
        }

        // INSERT

        // if purchase array empty will except
        $this->_manager->getRepository('AppBundle:Purchase\Purchase')->rawInsertPurchases($purchasesArray);

        $purchasesAggregated = $this->_manager->getRepository('AppBundle:Purchase\Purchase')->findSumsByStudentsWithSyncId(
            $data[self::SYNC_DATA][VendingMachineSync::getSyncArrayName()][0][self::VENDING_MACHINE_SYNC_ID]
        );

        foreach($purchasesAggregated as $purchase)
        {
            $totalLimit = $nfcTags->get($purchase['code'])->getStudent()->getTotalLimit();

            $totalLimit = $totalLimit - $purchase['price_sum'];

            $studentsArray[] = ['id' => $nfcTags->get($purchase['code'])->getStudent()->getId(), 'totalLimit' => $totalLimit];
        }

        // UPDATE

        // if student array empty will except
        $this->_manager->getRepository('AppBundle:Student\Student')->rawUpdateStudentsTotalLimits($studentsArray);
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

        // INSERT

        $this->_manager->getRepository('AppBundle:VendingMachine\VendingMachineEvent')->rawInsertVendingMachineEvents($eventsArray);
    }
}