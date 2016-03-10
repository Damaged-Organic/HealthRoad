<?php
// AppBundle/Service/Sync/SyncDataBuilder.php
namespace AppBundle\Service\Sync;

use Doctrine\ORM\PersistentCollection;

use AppBundle\Service\Sync\Utility\Interfaces\SyncDataInterface,
    AppBundle\Service\Sync\Utility\Checksum,
    AppBundle\Entity\VendingMachine\VendingMachineSync,
    AppBundle\Entity\Product\Product,
    AppBundle\Entity\NfcTag\NfcTag;

class SyncDataBuilder implements SyncDataInterface
{
    public $_checksum;

    public function setChecksum(Checksum $checksum)
    {
        $this->_checksum = $checksum;
    }

    public function buildSyncData(VendingMachineSync $vendingMachineSync = NULL)
    {
        $data = [];

        if( $vendingMachineSync )
            $data[] = $vendingMachineSync->getSyncObjectData();

        $data = [
            VendingMachineSync::getSyncArrayName() => $data
        ];

        $syncResponse = [
            self::SYNC_CHECKSUM => $this->_checksum->getDataChecksum($data),
            self::SYNC_DATA     => $data
        ];

        return $syncResponse;
    }

    public function buildProductData($products)
    {
        $data = [];

        foreach($products as $product) {
            $data[] = $product->getSyncObjectData();
        }

        $data = [
            Product::getSyncArrayName() => $data
        ];

        $syncResponse = [
            self::SYNC_CHECKSUM => $this->_checksum->getDataChecksum($data),
            self::SYNC_DATA     => $data
        ];

        return $syncResponse;
    }

    public function buildNfcTagData($students)
    {
        $build = function($nfcTag)
        {
            $data = $nfcTag->getSyncObjectData();

            $data[Product::getSyncArrayNameRestricted()] = NULL;

            foreach($nfcTag->getStudent()->getProducts() as $product) {
                $data[Product::getSyncArrayNameRestricted()][] = $product->getSyncObjectDataRestricted();
            }

            return $data;
        };

        $data = [];

        foreach($students as $student)
        {
            if( $student->getNfcTag() )
            {
                if( !$student->getNfcTag()->getPseudoDeleted() )
                    $data[] = $build($student->getNfcTag());
            }
        }

        $data = [
            NfcTag::getSyncArrayName() => $data
        ];

        $syncResponse = [
            self::SYNC_CHECKSUM => $this->_checksum->getDataChecksum($data),
            self::SYNC_DATA     => $data
        ];

        return $syncResponse;
    }
}
