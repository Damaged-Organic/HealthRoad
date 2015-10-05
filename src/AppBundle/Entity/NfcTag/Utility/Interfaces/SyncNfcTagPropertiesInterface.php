<?php
// AppBundle/Entity/NfcTag/Utility/Interfaces/SyncNfcTagPropertiesInterface.php
namespace AppBundle\Entity\NfcTag\Utility\Interfaces;

interface SyncNfcTagPropertiesInterface
{
    const NFC_ID                  = 'id';
    const NFC_DAILY_LIMIT         = 'daily-limit';
    const NFC_TOTAL_LIMIT         = 'total-limit';
    const NFC_RESTRICTED_PRODUCTS = 'restricted-products';
}