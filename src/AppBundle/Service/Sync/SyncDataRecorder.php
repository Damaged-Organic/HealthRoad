<?php
// AppBundle/Service/Sync/SyncDataRecorder.php
namespace AppBundle\Service\Sync;

use DateTime;

use Doctrine\ORM\EntityManager;

use AppBundle\Service\Sync\Utility\Interfaces\SyncDataInterface,
    AppBundle\Entity\VendingMachine\VendingMachine,
    AppBundle\Entity\VendingMachine\VendingMachineSync,
    AppBundle\Entity\VendingMachine\Utility\Interfaces\SyncVendingMachineSyncPropertiesInterface;

class SyncDataRecorder implements SyncDataInterface, SyncVendingMachineSyncPropertiesInterface
{
    protected $_manager;

    public function setManager(EntityManager $manager)
    {
        $this->_manager = $manager;
    }

    public function recordDataIfValid(VendingMachine $vendingMachine, $syncData, $recordMethod)
    {
        if( !$syncData[self::SYNC_CHECKSUM] || !$syncData[self::SYNC_DATA] )
            return FALSE;

        $vendingMachineSync = $recordMethod(
            $this->persistBaseData($vendingMachine, $syncData),
            $syncData
        );

        $this->_manager->persist($vendingMachineSync);
        $this->_manager->flush();

        return TRUE;
    }

    protected function persistBaseData(VendingMachine $vendingMachine, $syncData)
    {
        $vendingMachineSync = (new VendingMachineSync)
            ->setVendingMachine($vendingMachine)
            ->setSyncedAt(new DateTime)
            ->setChecksum($syncData[self::SYNC_CHECKSUM])
            ->setData(json_encode($syncData[self::SYNC_DATA]))
        ;

        // TODO: KLUDGE: Record latest sync date and time for VendingMachine:
        $vendingMachine->setVendingMachineSyncedAt($vendingMachineSync->getSyncedAt());
        $this->_manager->persist($vendingMachine);

        return $vendingMachineSync;
    }

    protected function recordSyncData(VendingMachineSync $vendingMachineSync)
    {
        $vendingMachineSync
            ->setVendingMachineSyncId(NULL)
            ->setSyncedType(self::VENDING_MACHINE_SYNC_TYPE_VENDING_MACHINE_SYNC)
        ;

        return $vendingMachineSync;
    }

    protected function recordProductData(VendingMachineSync $vendingMachineSync)
    {
        $vendingMachineSync
            ->setVendingMachineSyncId(NULL)
            ->setSyncedType(self::VENDING_MACHINE_SYNC_TYPE_PRODUCTS)
        ;

        return $vendingMachineSync;
    }

    protected function recordNfcTagData(VendingMachineSync $vendingMachineSync)
    {
        $vendingMachineSync
            ->setVendingMachineSyncId(NULL)
            ->setSyncedType(self::VENDING_MACHINE_SYNC_TYPE_NFC_TAGS)
        ;

        return $vendingMachineSync;
    }

    protected function recordVendingMachineData(VendingMachineSync $vendingMachineSync)
    {
        $vendingMachineSync
            ->setVendingMachineSyncId(NULL)
            ->setSyncedType(self::VENDING_MACHINE_SYNC_TYPE_VENDING_MACHINE)
        ;

        return $vendingMachineSync;
    }

    protected function recordVendingMachineEventData(VendingMachineSync $vendingMachineSync, $syncData)
    {
        $vendingMachineSync
            ->setVendingMachineSyncId(NULL)
            ->setSyncedType(self::VENDING_MACHINE_SYNC_TYPE_VENDING_MACHINE_EVENTS)
        ;

        return $vendingMachineSync;
    }

    protected function recordPurchaseData(VendingMachineSync $vendingMachineSync, $syncData)
    {
        $vendingMachineSync
            ->setVendingMachineSyncId($syncData[self::SYNC_DATA][self::VENDING_MACHINE_SYNC_ARRAY][0][self::VENDING_MACHINE_SYNC_ID])
            ->setSyncedType(self::VENDING_MACHINE_SYNC_TYPE_PURCHASES)
        ;

        return $vendingMachineSync;
    }

    protected function recordTransactionData(VendingMachineSync $vendingMachineSync, $syncData)
    {
        $vendingMachineSync
            ->setVendingMachineSyncId($syncData[self::SYNC_DATA][self::VENDING_MACHINE_SYNC_ARRAY][0][self::VENDING_MACHINE_SYNC_ID])
            ->setSyncedType(self::VENDING_MACHINE_SYNC_TYPE_TRANSACTIONS)
        ;

        return $vendingMachineSync;
    }
}
