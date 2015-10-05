<?php
// AppBundle/Entity/Purchase/Utility/Interfaces/SyncPurchasePropertiesInterface.php
namespace AppBundle\Entity\Purchase\Utility\Interfaces;

interface SyncPurchasePropertiesInterface
{
    const PURCHASE_ID            = 'id';
    const PURCHASE_DATETIME      = 'datetime';
    const PURCHASE_PRODUCT_ID    = 'product-id';
    const PURCHASE_PRODUCT_PRICE = 'product-price';
    const PURCHASE_NFC_ID        = 'nfc-id';
}