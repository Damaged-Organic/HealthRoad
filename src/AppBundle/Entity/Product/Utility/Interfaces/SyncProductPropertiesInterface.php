<?php
// AppBundle/Entity/Product/Utility/Interfaces/SyncProductPropertiesInterface.php
namespace AppBundle\Entity\Product\Utility\Interfaces;

interface SyncProductPropertiesInterface
{
    const PRODUCT_ARRAY = 'products';

    const PRODUCT_ID    = 'id';
    const PRODUCT_NAME  = 'name';
    const PRODUCT_PRICE = 'price';

    const PRODUCT_RESTRICTED_ARRAY = 'restricted-products';

    const PRODUCT_RESTRICTED_ID     = 'id';
    const PRODUCT_RESTRICTED_AMOUNT = 'amount';
}