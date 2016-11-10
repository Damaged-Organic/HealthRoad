<?php
// src/AppBundle/Entity/Transaction/Utility/Interfaces/SyncTransactionPropertiesInterface.php
namespace AppBundle\Entity\Transaction\Utility\Interfaces;

interface SyncTransactionPropertiesInterface
{
    const TRANSACTION_ARRAY = 'transactions';

    const TRANSACTION_SYNC_ID        = 'id';
    const TRANSACTION_TRANSACTION_AT = 'transaction-datetime';
    const TRANSACTION_NFC_CODE       = 'nfc-code';
    const TRANSACTION_STUDENT_ID     = 'student-id';
}
