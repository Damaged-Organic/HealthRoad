<?php
// src/AppBundle/Service/Payment/PaymentReceiptValidator.php
namespace AppBundle\Service\Payment;

use Symfony\Component\Validator\Validator\ValidatorInterface;

use Doctrine\ORM\EntityManager;

use AppBundle\Service\Payment\Utility\PaymentReceiptFileInterface,
    AppBundle\Validator\Constraints,
    AppBundle\Entity\Payment\PaymentReceipt;

class PaymentReceiptValidator implements PaymentReceiptFileInterface
{
    private $_manager;
    private $_validator;

    private $_isPriceConstraint;
    private $_isNfcTagNumberConstraint;

    public function setManager(EntityManager $_manager)
    {
        $this->_manager = $_manager;
    }

    public function setValidator(ValidatorInterface $validator)
    {
        $this->_validator = $validator;
    }

    private function isPriceConstraintValidator($probablyPrice)
    {
        $isPriceConstraint = ( $this->_isPriceConstraint ) ?: new Constraints\IsPriceConstraint;

        $errors = $this->_validator->validateValue($probablyPrice, $isPriceConstraint);

        return $errors;
    }

    private function IsNfcTagNumberConstraintValidator($probablyNfcTagNumber)
    {
        $isNfcTagNumberConstraint = ( $this->_isNfcTagNumberConstraint ) ?: new Constraints\IsNfcTagNumberConstraint;

        $errors = $this->_validator->validateValue($probablyNfcTagNumber, $isNfcTagNumberConstraint);

        return $errors;
    }

    public function validateAndMarkReceiptFields(array $orders)
    {
        foreach( $orders as $key => $order )
        {
            $nfcTagNumberValid = (
                !empty($order[self::RECEIPT_FIELD_NFC_TAG_NUMBER]) &&
                !count($this->IsNfcTagNumberConstraintValidator($order[self::RECEIPT_FIELD_NFC_TAG_NUMBER]))
            );

            $profitAmountValid = (
                !empty($order[self::RECEIPT_FIELD_PROFIT_AMOUNT]) &&
                !count($this->isPriceConstraintValidator($order[self::RECEIPT_FIELD_PROFIT_AMOUNT]))
            );

            $profitComissionValid = (
                !empty($order[self::RECEIPT_FIELD_PROFIT_COMISSION]) &&
                !count($this->isPriceConstraintValidator($order[self::RECEIPT_FIELD_PROFIT_COMISSION]))
            );

            $orders[$key][self::RECEIPT_FIELD_STATUS] = ( $nfcTagNumberValid && $profitAmountValid && $profitComissionValid )
                ? self::RECEIPT_VALID
                : self::RECEIPT_INVALID
            ;
        }

        return $orders;
    }

    public function validateAndMarkReceiptExistence(array $orders)
    {
        $checksumHashes = [];
        $nfcTagNumbers  = [];

        foreach( $orders as $id => $entry )
        {
            $paymentReceipt = (new PaymentReceipt)->constructFromPaymentReceiptFileEntry($entry);

            $checksumHashes[$id] = $paymentReceipt->getChecksumHash();
            $nfcTagNumbers[$id]  = $paymentReceipt->getNfcTagNumber();
        }

        //Check whether entry from payment receipt file already exists in database
        $paymentReceipts = $this->_manager->getRepository('AppBundle:Payment\PaymentReceipt')->findByChecksumHash($checksumHashes);

        $checksumHashesExisting = [];

        foreach( $paymentReceipts as $paymentReceipt ) {
            $checksumHashesExisting[] = $paymentReceipt->getChecksumHash();
        }

        $existingEntriesIdsByChecksumHashes = array_keys(array_intersect($checksumHashes, $checksumHashesExisting));

        //Check if NFC Tag number from payment receipt file exists in database and bounded to Student
        $nfcTags = $this->_manager->getRepository('AppBundle:NfcTag\NfcTag')->findByNfcTagNumber($nfcTagNumbers);

        $nfcTagNumbersExisting = [];
        $nfcTagNumbersUnbinded = [];

        foreach( $nfcTags as $nfcTag )
        {
            $nfcTagNumbersExisting[] = $nfcTag->getNumber();

            if( !$nfcTag->getStudent() )
                $nfcTagNumbersUnbinded[] = $nfcTag->getNumber();
        }

        $existingEntriesIdsByNfcTagNumbers = array_keys(array_intersect($nfcTagNumbers, $nfcTagNumbersExisting));
        $unbindedEntriesIdsByNfcTagNumbers = array_keys(array_intersect($nfcTagNumbers, $nfcTagNumbersUnbinded));

        //Mark corresponding entries which should be excluded from database insert
        foreach( $orders as $id => $entry )
        {
            if( $entry[self::RECEIPT_FIELD_STATUS] === self::RECEIPT_INVALID )
                continue;

            switch(TRUE)
            {
                case in_array($id, $existingEntriesIdsByChecksumHashes, TRUE):
                    $orders[$id][self::RECEIPT_FIELD_STATUS] = self::RECEIPT_EXISTS;
                break;

                case in_array($id, $unbindedEntriesIdsByNfcTagNumbers, TRUE):
                    $orders[$id][self::RECEIPT_FIELD_STATUS] = self::RECEIPT_UNBINDED;
                break;

                case !in_array($id, $existingEntriesIdsByNfcTagNumbers, TRUE):
                    $orders[$id][self::RECEIPT_FIELD_STATUS] = self::RECEIPT_MISMATCH;
                break;
            }
        }

        return $orders;
    }
}
