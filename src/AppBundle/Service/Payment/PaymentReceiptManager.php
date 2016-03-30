<?php
// src/AppBundle/Service/Payment/PaymentReceiptManager.php
namespace AppBundle\Service\Payment;

use Doctrine\ORM\EntityManager;

use AppBundle\Service\Payment\Utility\PaymentReceiptFileInterface,
    AppBundle\Entity\Payment\PaymentReceipt;

class PaymentReceiptManager implements PaymentReceiptFileInterface
{
    private $_manager;

    public function setManager(EntityManager $_manager)
    {
        $this->_manager = $_manager;
    }

    public function calculateAndSetProfits(array $orders)
    {
        foreach( $orders as $id => $entry )
        {
            if( $orders[$id][self::RECEIPT_FIELD_STATUS] == self::RECEIPT_VALID )
            {
                // $orders[$id][self::RECEIPT_FIELD_PROFIT] = bcsub(
                //     $entry[self::RECEIPT_FIELD_PROFIT_AMOUNT],
                //     $entry[self::RECEIPT_FIELD_PROFIT_COMISSION],
                //     2
                // );

                $orders[$id][self::RECEIPT_FIELD_PROFIT] = $entry[self::RECEIPT_FIELD_PROFIT_AMOUNT];
            }
        }

        return $orders;
    }

    public function findAndSetRelatedEntities(array $paymentReceipts)
    {
        $nfcTagNumbers  = [];

        foreach( $paymentReceipts as $paymentReceipt )
            $nfcTagNumbers[]  = $paymentReceipt->getNfcTagNumber();

        $nfcTags = $this->_manager->getRepository('AppBundle:NfcTag\NfcTag')->findByNfcTagNumberIndexedByNumber($nfcTagNumbers);

        foreach( $paymentReceipts as $paymentReceipt )
        {
            $paymentReceipt
                ->setNfcTag($nfcTags[$paymentReceipt->getNfcTagNumber()])
                ->setStudent(
                    $nfcTags[$paymentReceipt->getNfcTagNumber()]->getStudent()
                )
            ;
        }

        return $paymentReceipts;
    }

    public function replenishStudentsTotalLimit(array $paymentReceipts)
    {
        foreach( $paymentReceipts as $paymentReceipt )
        {
            $student = $paymentReceipt->getStudent();

            $student->setTotalLimit(
                bcadd($student->getTotalLimit(), $paymentReceipt->getProfit(), 2)
            );

            $this->_manager->persist($student);
        }
    }
}
