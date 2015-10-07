<?php
// AppBundle/Entity/VendingMachine/Utility/Interfaces/SyncVendingMachineSyncPropertiesInterface.php
namespace AppBundle\Entity\VendingMachine\Utility\Interfaces;

interface SyncVendingMachineSyncPropertiesInterface
{
    const VENDING_MACHINE_SYNC_ARRAY = 'sync';

    const VENDING_MACHINE_SYNC_ID   = 'sync-id';
    const VENDING_MACHINE_SYNC_TYPE = 'type';

    const VENDING_MACHINE_SYNC_TYPE_PRODUCTS        = 'sync_get_vending_machines_products';
    const VENDING_MACHINE_SYNC_TYPE_NFC_TAGS        = 'sync_get_vending_machines_nfc_tags';
    const VENDING_MACHINE_SYNC_TYPE_SYNC            = 'sync_get_vending_machines_sync';
    const VENDING_MACHINE_SYNC_TYPE_VENDING_MACHINE = 'sync_put_vending_machines';
}