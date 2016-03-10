<?php
// src/AppBundle/Service/Payment/PaymentReceiptParser.php
namespace AppBundle\Service\Payment;

use AppBundle\Service\Payment\Utility\PaymentReceiptFileInterface;

class PaymentReceiptParser implements PaymentReceiptFileInterface
{
    const DELIMITER = ';';

    public function parseReceiptFile($ordersFile)
    {
        $parser = function($input) {
            return str_getcsv($input, self::DELIMITER);
        };

        return ( file_exists($ordersFile) ) ? array_map($parser, file($ordersFile)) : NULL;
    }

    public function standardizeReceipt(array $orders)
    {
        array_shift($orders);

        foreach( $orders as $key => $order )
        {
            if( !empty($order[self::RECEIPT_FIELD_NFC_TAG_NUMBER]) )
                $orders[$key][self::RECEIPT_FIELD_NFC_TAG_NUMBER] = $this->standardizeNfcTagNumber($order[self::RECEIPT_FIELD_NFC_TAG_NUMBER]);
        }

        return $orders;
    }

    /**
     * This method tries to standardize presumably "dirty" NFC tag number.
     * Standardizing includes trimming, whitespace removal, uppercase convertion
     * and replacing cyrillic characters with latin ones.
     */
    private function standardizeNfcTagNumber($nfcTagNumber)
    {
        return preg_replace('/\s+/', '', trim(strtr(mb_strtoupper($nfcTagNumber, 'UTF-8'), $this->getCharactersMap())));
    }

    /**
     * Provides cyrillic characters that may be confused with latin ones, and
     * their corresponding latin representation.
     */
    private function getCharactersMap()
    {
        return [
            'У' => 'Y',
            'К' => 'K',
            'Е' => 'E',
            'Н' => 'H',
            'Х' => 'X',
            'В' => 'B',
            'А' => 'A',
            'Р' => 'P',
            'О' => 'O',
            'С' => 'C',
            'М' => 'M',
            'Т' => 'T',
            'І' => 'I',
        ];
    }
}
