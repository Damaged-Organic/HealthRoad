<?php
// AppBundle/Service/Sync/SyncDataRecorder.php
namespace AppBundle\Service\Sync;

use DateTime;

use Symfony\Component\Security\Core\Exception\BadCredentialsException;

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

    public function recordProductData(VendingMachine $vendingMachine, $syncResponse)
    {
        if( !$syncResponse[self::SYNC_CHECKSUM] || !$syncResponse[self::SYNC_DATA] )
            throw new BadCredentialsException('Sync response array is missing required data');

        $vendingMachineSync = (new VendingMachineSync)
            ->setVendingMachine($vendingMachine)
            ->setVendingMachineSyncId(NULL)
            ->setSyncedType(self::VENDING_MACHINE_SYNC_TYPE_PRODUCTS)
            ->setSyncedAt(new DateTime)
            ->setChecksum($syncResponse[self::SYNC_CHECKSUM])
            ->setData(json_encode($syncResponse[self::SYNC_DATA]))
        ;

        $this->_manager->persist($vendingMachineSync);
        $this->_manager->flush();
    }

    public function recordNfcTagData(VendingMachine $vendingMachine, $syncResponse)
    {
        if( !$syncResponse[self::SYNC_CHECKSUM] || !$syncResponse[self::SYNC_DATA] )
            throw new BadCredentialsException('Sync response array is missing required data');

        $vendingMachineSync = (new VendingMachineSync)
            ->setVendingMachine($vendingMachine)
            ->setVendingMachineSyncId(NULL)
            ->setSyncedType(self::VENDING_MACHINE_SYNC_TYPE_NFC_TAGS)
            ->setSyncedAt(new DateTime)
            ->setChecksum($syncResponse[self::SYNC_CHECKSUM])
            ->setData(json_encode($syncResponse[self::SYNC_DATA]))
        ;

        $this->_manager->persist($vendingMachineSync);
        $this->_manager->flush();
    }

    public function recordSyncData(VendingMachine $vendingMachine, $syncResponse)
    {
        if( !$syncResponse[self::SYNC_CHECKSUM] || !$syncResponse[self::SYNC_DATA] )
            throw new BadCredentialsException('Sync response array is missing required data');

        $vendingMachineSync = (new VendingMachineSync)
            ->setVendingMachine($vendingMachine)
            ->setVendingMachineSyncId(NULL)
            ->setSyncedType(self::VENDING_MACHINE_SYNC_TYPE_VENDING_MACHINE_SYNC)
            ->setSyncedAt(new DateTime)
            ->setChecksum($syncResponse[self::SYNC_CHECKSUM])
            ->setData(json_encode($syncResponse[self::SYNC_DATA]))
        ;

        $this->_manager->persist($vendingMachineSync);
        $this->_manager->flush();
    }

    public function recordVendingMachineData(VendingMachine $vendingMachine, $syncRequest)
    {
        if( !$syncRequest[self::SYNC_CHECKSUM] || !$syncRequest[self::SYNC_DATA] )
            throw new BadCredentialsException('Sync response array is missing required data');

        $vendingMachineSync = (new VendingMachineSync)
            ->setVendingMachine($vendingMachine)
            ->setVendingMachineSyncId($syncRequest[self::SYNC_DATA][self::VENDING_MACHINE_SYNC_ARRAY][0][self::VENDING_MACHINE_SYNC_ID])
            ->setSyncedType(self::VENDING_MACHINE_SYNC_TYPE_VENDING_MACHINE)
            ->setSyncedAt(new DateTime)
            ->setChecksum($syncRequest[self::SYNC_CHECKSUM])
            ->setData(json_encode($syncRequest[self::SYNC_DATA]))
        ;

        $this->_manager->persist($vendingMachineSync);
        $this->_manager->flush();
    }

    public function recordPurchaseData($vendingMachine, $syncRequest)
    {
        if( !$syncRequest[self::SYNC_CHECKSUM] || !$syncRequest[self::SYNC_DATA] )
            throw new BadCredentialsException('Sync response array is missing required data');

        $vendingMachineSync = (new VendingMachineSync)
            ->setVendingMachine($vendingMachine)
            ->setVendingMachineSyncId($syncRequest[self::SYNC_DATA][self::VENDING_MACHINE_SYNC_ARRAY][0][self::VENDING_MACHINE_SYNC_ID])
            ->setSyncedType(self::VENDING_MACHINE_SYNC_TYPE_PURCHASES)
            ->setSyncedAt(new DateTime)
            ->setChecksum($syncRequest[self::SYNC_CHECKSUM])
            ->setData(json_encode($syncRequest[self::SYNC_DATA]))
        ;

        $this->_manager->persist($vendingMachineSync);
    }
}