<?php
// src/AppBundle/Service/Payment/PaymentLiqPayManager.php
namespace AppBundle\Service\Payment;

use DateTime;

use LiqPay;

use Symfony\Bundle\FrameworkBundle\Routing\Router;

use AppBundle\Entity\Student\Student;

class PaymentLiqPayManager
{
    const LIQ_PAY_VERSION = 3;
    const LIQ_PAY_SANDBOX = 0;

    protected $_router;

    protected $_liqPay;
    protected $liqPayKeys;

    public function setRouter(Router $router)
    {
        $this->_router = $router;
    }

    public function setLiqPay(LiqPay $liqPay)
    {
        $this->_liqPay  = $liqPay;
    }

    public function setLiqPayKeys($liqPayKeyPublic, $liqPayKeyPrivate)
    {
        $this->liqPayKeys = [
            'public'  => $liqPayKeyPublic,
            'private' => $liqPayKeyPrivate
        ];
    }

    public function getPublicKey()
    {
        return $this->liqPayKeys['public'];
    }

    public function getPrivateKey()
    {
        return $this->liqPayKeys['private'];
    }

    public function getOrderId(Student $student)
    {
        $customerId = ( $student->getCustomer() )
            ? $student->getCustomer()->getId()
            : NULL;

        $dateTime = (new DateTime())->format('d-m-Y H:i:s');

        return hash('sha256',
            $student->getId() . $customerId . $dateTime
        );
    }

    public function getResultUrl(Student $student)
    {
        return $this->_router->generate('customer_office_students', [
            'id' => $student->getId()
        ], TRUE);
    }

    public function getDescription(Student $student)
    {
        $nfcTagNumber = ( $student->getNfcTag() )
            ? $student->getNfcTag()->getNumber()
            : NULL;

        return "Поповнення рахунку для студента {$student->getFullName()}" . (( $nfcTagNumber ) ? " (за номером картки {$nfcTagNumber})" : "");
    }

    public function getFormParameters($orderId, $amount, $description, $resultUrl)
    {
        return [
            'version'     => self::LIQ_PAY_VERSION,
            'sandbox'     => self::LIQ_PAY_SANDBOX,
            'order_id'    => $orderId,
            'action'      => 'pay',
            'pay_way'     => 'card,liqpay,privat24',
            'currency'    => 'UAH',
            'amount'      => $amount,
            'description' => $description,
            'result_url'  => $resultUrl,
            'language'    => 'ru'
        ];
    }

    public function getCnbFormAction()
    {
        return $this->_liqPay->getCheckoutUrl();
    }

    public function getCnbFormData(array $parameters)
    {
        $parameters = $this->_liqPay->cnb_params($parameters);

        return base64_encode(json_encode($parameters));
    }

    public function getCnbFormSignature(array $parameters)
    {
        $parameters = $this->_liqPay->cnb_params($parameters);

        return $this->_liqPay->cnb_signature($parameters);
    }
}
