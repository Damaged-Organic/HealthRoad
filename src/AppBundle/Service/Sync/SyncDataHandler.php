<?php
// AppBundle/Service/Sync/SyncDataHandler.php
namespace AppBundle\Service\Sync;

use AppBundle\Entity\VendingMachine\Utility\Interfaces\SyncVendingMachineSyncPropertiesInterface;
use Doctrine\ORM\EntityManager;

class SyncDataHandler implements
    SyncVendingMachineSyncPropertiesInterface
{
    private $_manager;

    public function setManager(EntityManager $manager)
    {
        $this->_manager = $manager;
    }

    public function handleVendingMachineSyncData($data)
    {
        $vendingMachineSync = $this->_manager->getRepository('AppBundle:VendingMachine\VendingMachineSync')
            ->findLatestByVendingMachineSyncType($data[self::VENDING_MACHINE_SYNC_TYPE]);

        return $vendingMachineSync;
    }
}