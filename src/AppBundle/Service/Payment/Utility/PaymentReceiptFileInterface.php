<?php
// src/AppBundle/Service/Payment/Utility/PaymentReceiptFileInterface.php
namespace AppBundle\Service\Payment\Utility;

interface PaymentReceiptFileInterface
{
    const RECEIPT_FIELDS_COUNT = 13;

    const RECEIPT_FIELD_NFC_TAG_NUMBER   = 6;
    const RECEIPT_FIELD_PROFIT_AMOUNT    = 7;
    const RECEIPT_FIELD_PROFIT_COMISSION = 8;

    const RECEIPT_FIELD_STATUS = 'receipt_field_status';
    const RECEIPT_FIELD_PROFIT = 'receipt_field_profit';

    const RECEIPT_VALID    = 'valid';
    const RECEIPT_EXISTS   = 'exists';
    const RECEIPT_MISMATCH = 'mismatch';
    const RECEIPT_UNBINDED = 'unbinded';
    const RECEIPT_INVALID  = 'invalid';
}
