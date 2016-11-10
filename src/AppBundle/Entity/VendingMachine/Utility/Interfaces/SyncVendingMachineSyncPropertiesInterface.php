<?php
// AppBundle/Entity/VendingMachine/Utility/Interfaces/SyncVendingMachineSyncPropertiesInterface.php
namespace AppBundle\Entity\VendingMachine\Utility\Interfaces;

interface SyncVendingMachineSyncPropertiesInterface
{
    const VENDING_MACHINE_SYNC_ARRAY = 'sync';

    const VENDING_MACHINE_SYNC_ID   = 'id';
    const VENDING_MACHINE_SYNC_TYPE = 'type';

    const VENDING_MACHINE_SYNC_TYPE_PRODUCTS               = 'get_vending_machines_products';
    const VENDING_MACHINE_SYNC_TYPE_NFC_TAGS               = 'get_vending_machines_nfc_tags';
    const VENDING_MACHINE_SYNC_TYPE_PURCHASES              = 'post_vending_machines_purchases';
    const VENDING_MACHINE_SYNC_TYPE_TRANSACTIONS           = 'post_vending_machines_transactions';
    const VENDING_MACHINE_SYNC_TYPE_VENDING_MACHINE        = 'put_vending_machines';
    const VENDING_MACHINE_SYNC_TYPE_VENDING_MACHINE_EVENTS = 'post_vending_machines_events';
    const VENDING_MACHINE_SYNC_TYPE_VENDING_MACHINE_SYNC   = 'get_vending_machines_sync';
}
