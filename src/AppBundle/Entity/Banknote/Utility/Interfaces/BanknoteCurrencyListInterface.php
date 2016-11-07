<?php
// src/AppBundle/Entity/Banknote/Utility/Interfaces/BanknoteCurrencyListInterface.php
namespace AppBundle\Entity\Banknote\Utility\Interfaces;

interface BanknoteCurrencyListInterface
{
    const BANKNOTE_CURRENCY_UAH = 'UAH';

    static public function getBanknoteCurrencyList();
}
