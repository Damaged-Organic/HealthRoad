<?php
// AppBundle/Service/Sync/SyncDataBuilder.php
namespace AppBundle\Service\Sync;

use Doctrine\ORM\PersistentCollection;

use AppBundle\Service\Sync\Utility\Interfaces\SyncDataInterface,
    AppBundle\Service\Sync\Utility\Checksum,
    AppBundle\Entity\VendingMachine\VendingMachine,
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

    public function buildProductData(PersistentCollection $products)
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

    public function buildNfcTagData(PersistentCollection $nfcTags)
    {
        $data = [];

        $build = function($nfcTag)
        {
            $data = $nfcTag->getSyncObjectData();

            $data[Product::getSyncArrayNameRestricted()] = NULL;

            foreach($nfcTag->getStudent()->getProducts() as $product) {
                $data[Product::getSyncArrayNameRestricted()][] = $product->getSyncObjectDataRestricted();
            }

            return $data;
        };

        foreach($nfcTags as $nfcTag)
        {
            $data[] = $build($nfcTag);
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

    public function buildSyncData(VendingMachineSync $vendingMachineSync = NULL)
    {
        $data = NULL;

        if( $vendingMachineSync ) {
            $data[] = $vendingMachineSync->getSyncObjectData();
        }

        $data = [
            VendingMachineSync::getSyncArrayName() => $data
        ];

        $syncResponse = [
            self::SYNC_CHECKSUM => $this->_checksum->getDataChecksum($data),
            self::SYNC_DATA     => $data
        ];

        return $syncResponse;
    }
}