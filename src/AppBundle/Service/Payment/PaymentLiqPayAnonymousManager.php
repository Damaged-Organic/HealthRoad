<?php
// src/AppBundle/Service/Payment/PaymentLiqPayAnonymousManager.php
namespace AppBundle\Service\Payment;

use DateTime;

use AppBundle\Service\Payment\PaymentLiqPayManager;

use AppBundle\Entity\Student\Student;

class PaymentLiqPayAnonymousManager extends PaymentLiqPayManager
{
    public function getOrderId(Student $student)
    {
        $nfcTagNumber = ( $student->getNfcTag() )
            ? $student->getNfcTag()->getNumber()
            : NULL;

        $dateTime = (new DateTime())->format('d-m-Y H:i:s');

        return hash('sha256',
            $nfcTagNumber . $dateTime
        );
    }

    public function getResultUrl(Student $student)
    {
        return $this->_router->generate('customer_office_replenish', [], TRUE);
    }

    public function getDescription(Student $student)
    {
        $nfcTagNumber = ( $student->getNfcTag() )
            ? $student->getNfcTag()->getNumber()
            : NULL;

        return "Поповнення рахунку для студента {$student->getFullName()}" . (( $nfcTagNumber ) ? " (за номером картки {$nfcTagNumber})" : "");
    }
}
