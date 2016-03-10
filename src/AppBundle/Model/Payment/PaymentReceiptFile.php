<?php
// src/AppBundle/Model/Payment/PaymentReceipt.php
namespace AppBundle\Model\Payment;

use Symfony\Component\Validator\Constraints as Assert;

class PaymentReceiptFile
{
    /**
     * @Assert\NotBlank(message="payment_receipt.payment_receipt_file.not_blank")
     * @Assert\File(
     *     maxSize="50M",
     *     mimeTypes={"text/csv", "text/plain"},
     *     maxSizeMessage="payment_receipt.payment_receipt_file.max_size",
     *     mimeTypesMessage="payment_receipt.payment_receipt_file.mime_types"
     * )
     */
    protected $paymentReceiptFile;

    public function setPaymentReceiptFile($paymentReceiptFile)
    {
        $this->paymentReceiptFile = $paymentReceiptFile;

        return $this;
    }

    public function getPaymentReceiptFile()
    {
        return $this->paymentReceiptFile;
    }
}
