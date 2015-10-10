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

    public function validateSyncSequence($vendingMachine, $type, $data)
    {
        $vendingMachineSync = $this->_manager->getRepository('AppBundle:VendingMachine\VendingMachineSync')->findOneBy([
            'vendingMachine'       => $vendingMachine,
            'vendingMachineSyncId' => $data[self::SYNC_DATA][self::VENDING_MACHINE_SYNC_ARRAY][0][self::VENDING_MACHINE_SYNC_ID],
            'syncedType'           => $type
        ]);

        return $vendingMachineSync;
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
        $products = $vendingMachine->getProducts();
        $nfcTags = new ArrayCollection($this->_manager->getRepository('AppBundle:NfcTag\NfcTag')->findByVendingMachine($vendingMachine));

        $purchasesArray = [];

        foreach( $data[self::SYNC_DATA][Purchase::getSyncArrayName()] as $value )
        {
            $purchase = (new Purchase)
                ->setSyncPurchaseId($value[Purchase::PURCHASE_SYNC_ID])
                ->setSyncPurchasedAt(new DateTime($value[Purchase::PURCHASE_PURCHASED_AT]))
            ;

            $purchase
                ->setVendingMachine($vendingMachine)
                ->setVendingMachineSerial($vendingMachine->getSerial())
                ->setVendingMachineSyncId($data[self::SYNC_DATA][VendingMachineSync::getSyncArrayName()][0][self::VENDING_MACHINE_SYNC_ID])
            ;

            $purchase
                ->setSyncProductId($value[Purchase::PURCHASE_PRODUCT_ID])
                ->setSyncProductPrice($value[Purchase::PURCHASE_SYNC_PRODUCT_PRICE])
                ->setProduct(
                    ( $products->get($value[Purchase::PURCHASE_PRODUCT_ID]) ) ? $products->get($value[Purchase::PURCHASE_PRODUCT_ID]) : NULL
                )
            ;

            $purchase
                ->setSyncNfcTagCode($value[Purchase::PURCHASE_NFC_CODE])
                ->setNfcTag(
                    ( $nfcTags->get($value[Purchase::PURCHASE_NFC_CODE]) ) ? $nfcTags->get($value[Purchase::PURCHASE_NFC_CODE]) : NULL
                )
            ;

            //$this->_manager->persist($purchase);

            //$totalLimit = $purchase->getNfcTag()->getStudent()->getTotalLimit();

            //$totalLimit = $totalLimit - $purchase->getProduct()->getPrice();

            //$purchase->getNfcTag()->getStudent()->setTotalLimit($totalLimit);

            //$this->_manager->persist($purchase);
            $purchasesArray[] = $purchase;

            //$students[] = ['id' => $purchase->getNfcTag()->getStudent()->getId(), 'totalLimit' => $totalLimit];
        }

        //$this->_manager->flush();

        // INSERT

        $values = '';

        foreach( $purchasesArray as $purchase )
        {
            $values .= " ('{$purchase->getVendingMachine()->getId()}', '{$purchase->getProduct()->getId()}', '{$purchase->getNfcTag()->getId()}',
                '{$purchase->getSyncPurchaseId()}', '{$purchase->getSyncNfcTagCode()}', '{$purchase->getSyncProductId()}', '{$purchase->getSyncProductPrice()}', '{$purchase->getSyncPurchasedAt()->format('d-m-Y H:i:s')}', '{$purchase->getVendingMachineSerial()}', '{$purchase->getVendingMachineSyncId()}'),";
        }

        $values = substr($values, 0, -1);

        $sql = "INSERT INTO purchases (vending_machine_id, product_id, nfc_tag_id, sync_purchase_id, sync_nfc_tag_code, sync_product_id, sync_product_price, sync_purchased_at, vending_machine_serial, vending_machine_sync_id) VALUES " . $values;
        $stmt = $this->_manager->getConnection()->prepare($sql);
        $result = $stmt->execute();

        // UPDATE

        $purchasesAggregated = $this->_manager->getRepository('AppBundle:Purchase\Purchase')->findSumsByStudentsWithSyncId(
            $data[self::SYNC_DATA][VendingMachineSync::getSyncArrayName()][0][self::VENDING_MACHINE_SYNC_ID]
        );

        foreach($purchasesAggregated as $purchase)
        {
            $totalLimit = $nfcTags->get($purchase['code'])->getStudent()->getTotalLimit();

            $totalLimit = $totalLimit - $purchase['price_sum'];

            $students[] = ['id' => $nfcTags->get($purchase['code'])->getStudent()->getId(), 'totalLimit' => $totalLimit];
        }

        $when = $id = '';
        foreach($students as $student)
        {
            $when .= " WHEN {$student['id']} THEN '{$student['totalLimit']}' ";
            $id .= "{$student['id']},";
        }

        $id = substr($id, 0, -1);

        $sql = "UPDATE students SET total_limit = (CASE id " . $when . " END) WHERE id IN (" . $id . ")";
        $stmt = $this->_manager->getConnection()->prepare($sql);
        $result = $stmt->execute();
    }

    public function handleVendingMachineEventData(VendingMachine $vendingMachine, $data)
    {
        $eventsArray = [];

        foreach( $data[self::SYNC_DATA][Purchase::getSyncArrayName()] as $value )
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

        $values = '';

        foreach( $eventsArray as $event )
        {
            $values .= " ('{$event->getVendingMachine()->getId()}',
                '{$event->getSyncEventId()}', '{$event->getOccurredAt()}', '{$event->getType()}', '{$event->getCode()}', '{$event->getMessage()}'),";
        }

        $values = substr($values, 0, -1);

        $sql = "INSERT INTO purchases (vending_machine_id, sync_event_id, occurred_at, type, code, message) VALUES " . $values;
        $stmt = $this->_manager->getConnection()->prepare($sql);
        $result = $stmt->execute();
    }
}