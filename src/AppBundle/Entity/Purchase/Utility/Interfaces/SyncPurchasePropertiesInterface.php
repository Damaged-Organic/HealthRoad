<?php
// AppBundle/Entity/Purchase/Utility/Interfaces/SyncPurchasePropertiesInterface.php
namespace AppBundle\Entity\Purchase\Utility\Interfaces;

interface SyncPurchasePropertiesInterface
{
    const PURCHASE_ARRAY = 'purchases';

    const PURCHASE_SYNC_ID            = 'id';
    const PURCHASE_SYNC_PRODUCT_PRICE = 'product-price';
    const PURCHASE_PRODUCT_ID         = 'product-id';
    const PURCHASE_PURCHASED_AT       = 'purchase-datetime';
    const PURCHASE_NFC_CODE           = 'nfc-code';
}