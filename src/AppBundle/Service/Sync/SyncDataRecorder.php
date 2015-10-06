<?php
// AppBundle/Service/Sync/SyncDataRecorder.php
namespace AppBundle\Service\Sync;

use DateTime;

use Symfony\Component\Security\Core\Exception\BadCredentialsException;

use Doctrine\ORM\EntityManager;

use AppBundle\Service\Sync\Utility\Interfaces\SyncDataInterface,
    AppBundle\Entity\VendingMachine\VendingMachine,
    AppBundle\Entity\VendingMachine\VendingMachineSync;

class SyncDataRecorder implements SyncDataInterface
{
    protected $_manager;

    public function __construct(EntityManager $manager)
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
            ->setSyncedType(self::SYNC_TYPE_PRODUCTS)
            ->setSyncedAt(new DateTime)
            ->setChecksum($syncResponse[self::SYNC_CHECKSUM])
            ->setData(json_encode($syncResponse[self::SYNC_DATA]))
        ;

        $this->_manager->persist($vendingMachineSync);
        $this->_manager->flush();
    }
}