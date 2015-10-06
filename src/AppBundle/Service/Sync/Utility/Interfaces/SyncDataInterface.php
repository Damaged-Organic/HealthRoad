<?php
// AppBundle/Service/Sync/Utility/Interfaces/SyncDataInterface.php
namespace AppBundle\Service\Sync\Utility\Interfaces;

interface SyncDataInterface
{
    const SYNC_DATA     = 'data';
    const SYNC_CHECKSUM = 'checksum';

    const SYNC_TYPE_PRODUCTS = 'sync_get_vending_machines_products';
    const SYNC_TYPE_NFC_TAGS = 'sync_get_vending_machines_nfc_tags';
}