<?php
// AppBundle/Entity/NfcTag/Utility/Interfaces/SyncNfcTagPropertiesInterface.php
namespace AppBundle\Entity\NfcTag\Utility\Interfaces;

interface SyncNfcTagPropertiesInterface
{
    const NFC_TAG_ARRAY = 'nfc-tags';

    const NFC_TAG_CODE                = 'code';
    const NFC_TAG_DAILY_LIMIT         = 'daily-limit';
    const NFC_TAG_TOTAL_LIMIT         = 'total-limit';
    const NFC_TAG_RESTRICTED_PRODUCTS = 'restricted-products';
}