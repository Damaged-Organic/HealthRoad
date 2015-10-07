<?php
// AppBundle/Entity/Purchase/Utility/Interfaces/SyncPurchasePropertiesInterface.php
namespace AppBundle\Entity\Purchase\Utility\Interfaces;

interface SyncPurchasePropertiesInterface
{
    const PURCHASE_ID_SYNC            = 'id';
    const PURCHASE_PURCHASED_AT       = 'purchased-at';
    const PURCHASE_PRODUCT_ID         = 'product-id';
    const PURCHASE_PRODUCT_PRICE_SYNC = 'product-price';
    const PURCHASE_NFC_CODE           = 'nfc-code';
}