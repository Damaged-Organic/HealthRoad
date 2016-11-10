<?php
// src/AppBundle/Entity/Banknote/Utility/Interfaces/SyncBanknotePropertiesInterface.php
namespace AppBundle\Entity\Banknote\Utility\Interfaces;

interface SyncBanknotePropertiesInterface
{
    const BANKNOTE_ARRAY = 'banknotes';

    const BANKNOTE_CURRENCY      = 'currency';
    const BANKNOTE_NOMINAL       = 'nominal';
    const BANKNOTE_LIST_QUANTITY = 'quantity';
}
