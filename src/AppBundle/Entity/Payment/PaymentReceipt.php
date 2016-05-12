<?php
// src/AppBundle/Entity/Payment/PaymentReceipt.php
namespace AppBundle\Entity\Payment;

use DateTime;

use Doctrine\ORM\Mapping as ORM;

use AppBundle\Entity\Utility\Traits\DoctrineMapping\IdMapperTrait,
    AppBundle\Service\Payment\Utility\PaymentReceiptFileInterface;

/**
 * @ORM\Table(name="payments_receipts")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Payment\Repository\PaymentReceiptRepository")
 */
class PaymentReceipt implements PaymentReceiptFileInterface
{
    use IdMapperTrait;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\NfcTag\NfcTag", inversedBy="paymentsReceipts")
     * @ORM\JoinColumn(name="nfc_tag_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $nfcTag;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Student\Student", inversedBy="paymentsReceipts")
     * @ORM\JoinColumn(name="student_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $student;

    /**
     * @ORM\Column(type="string", length=20)
     */
    protected $receiptNumber;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $receiptDate;

    /**
     * @ORM\Column(type="string", length=26)
     */
    protected $documentNumber;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $operationalDate;

    /**
     * @ORM\Column(type="string", length=8)
     */
    protected $nfcTagNumber;

    /**
     * @ORM\Column(type="string", length=500)
     */
    protected $payerFullName;

    /**
     * @ORM\Column(type="string", length=500)
     */
    protected $paymentPurpose;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    protected $paymentAmount;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    protected $paymentComission;

    /**
     * @ORM\Column(type="integer")
     */
    protected $paymentNumbers;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    protected $paymentAmountTotal;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    protected $paymentComissionTotal;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    protected $resultAmount;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    protected $profit;

    /**
     * @ORM\Column(type="string", length=64, unique=true)
     */
    protected $checksumHash;

    protected $status;

    public function getSearchProperties()
    {
        $searchProperties = [
            $this->getReceiptNumber(),
            $this->getNfcTagNumber(),
            $this->getPayerFullName(),
        ];

        if( $this->getReceiptDate() ) {
            $searchProperties[] = $this->getReceiptDate()->format('Y-m-d');
        }

        return $searchProperties;
    }

    public function constructFromPaymentReceiptFileEntry(array $entry)
    {
        $this
            ->setReceiptNumber($entry[0])
            ->setReceiptDate(new DateTime($entry[1]))
            ->setDocumentNumber($entry[2])
            ->setOperationalDate(new DateTime($entry[3]))
            ->setNfcTagNumber($entry[6])
            ->setPayerFullName($entry[4])
            ->setPaymentPurpose($entry[5])
            ->setPaymentAmount($entry[7])
            ->setPaymentComission($entry[8])
            ->setPaymentNumbers($entry[9])
            ->setPaymentAmountTotal($entry[10])
            ->setPaymentComissionTotal($entry[11])
            ->setResultAmount($entry[12])
        ;

        $this->setChecksumHash(
            $this->generateChecksumHash()
        );

        if( !empty($entry[self::RECEIPT_FIELD_PROFIT]) )
            $this->setProfit($entry[self::RECEIPT_FIELD_PROFIT]);

        if( !empty($entry[self::RECEIPT_FIELD_STATUS]) )
            $this->setStatus($entry[self::RECEIPT_FIELD_STATUS]);

        return $this;
    }

    /**
     * Set receiptNumber
     *
     * @param string $receiptNumber
     * @return PaymentReceipt
     */
    public function setReceiptNumber($receiptNumber)
    {
        $this->receiptNumber = $receiptNumber;

        return $this;
    }

    /**
     * Get receiptNumber
     *
     * @return string
     */
    public function getReceiptNumber()
    {
        return $this->receiptNumber;
    }

    /**
     * Set receiptDate
     *
     * @param \DateTime $receiptDate
     * @return PaymentReceipt
     */
    public function setReceiptDate($receiptDate)
    {
        $this->receiptDate = $receiptDate;

        return $this;
    }

    /**
     * Get receiptDate
     *
     * @return \DateTime
     */
    public function getReceiptDate()
    {
        return $this->receiptDate;
    }

    /**
     * Set documentNumber
     *
     * @param string $documentNumber
     * @return PaymentReceipt
     */
    public function setDocumentNumber($documentNumber)
    {
        $this->documentNumber = $documentNumber;

        return $this;
    }

    /**
     * Get documentNumber
     *
     * @return string
     */
    public function getDocumentNumber()
    {
        return $this->documentNumber;
    }

    /**
     * Set operationalDate
     *
     * @param \DateTime $operationalDate
     * @return PaymentReceipt
     */
    public function setOperationalDate($operationalDate)
    {
        $this->operationalDate = $operationalDate;

        return $this;
    }

    /**
     * Get operationalDate
     *
     * @return \DateTime
     */
    public function getOperationalDate()
    {
        return $this->operationalDate;
    }

    /**
     * Set nfcTagNumber
     *
     * @param string $nfcTagNumber
     * @return PaymentReceipt
     */
    public function setNfcTagNumber($nfcTagNumber)
    {
        $this->nfcTagNumber = $nfcTagNumber;

        return $this;
    }

    /**
     * Get nfcTagNumber
     *
     * @return string
     */
    public function getNfcTagNumber()
    {
        return $this->nfcTagNumber;
    }

    /**
     * Set payerFullName
     *
     * @param string $payerFullName
     * @return PaymentReceipt
     */
    public function setPayerFullName($payerFullName)
    {
        $this->payerFullName = $payerFullName;

        return $this;
    }

    /**
     * Get payerFullName
     *
     * @return string
     */
    public function getPayerFullName()
    {
        return $this->payerFullName;
    }

    /**
     * Set paymentPurpose
     *
     * @param string $paymentPurpose
     * @return PaymentReceipt
     */
    public function setPaymentPurpose($paymentPurpose)
    {
        $this->paymentPurpose = $paymentPurpose;

        return $this;
    }

    /**
     * Get paymentPurpose
     *
     * @return string
     */
    public function getPaymentPurpose()
    {
        return $this->paymentPurpose;
    }

    /**
     * Set paymentAmount
     *
     * @param string $paymentAmount
     * @return PaymentReceipt
     */
    public function setPaymentAmount($paymentAmount)
    {
        $this->paymentAmount = $paymentAmount;

        return $this;
    }

    /**
     * Get paymentAmount
     *
     * @return string
     */
    public function getPaymentAmount()
    {
        return $this->paymentAmount;
    }

    /**
     * Set paymentComission
     *
     * @param string $paymentComission
     * @return PaymentReceipt
     */
    public function setPaymentComission($paymentComission)
    {
        $this->paymentComission = $paymentComission;

        return $this;
    }

    /**
     * Get paymentComission
     *
     * @return string
     */
    public function getPaymentComission()
    {
        return $this->paymentComission;
    }

    /**
     * Set paymentNumbers
     *
     * @param integer $paymentNumbers
     * @return PaymentReceipt
     */
    public function setPaymentNumbers($paymentNumbers)
    {
        $this->paymentNumbers = $paymentNumbers;

        return $this;
    }

    /**
     * Get paymentNumbers
     *
     * @return integer
     */
    public function getPaymentNumbers()
    {
        return $this->paymentNumbers;
    }

    /**
     * Set paymentAmountTotal
     *
     * @param string $paymentAmountTotal
     * @return PaymentReceipt
     */
    public function setPaymentAmountTotal($paymentAmountTotal)
    {
        $this->paymentAmountTotal = $paymentAmountTotal;

        return $this;
    }

    /**
     * Get paymentAmountTotal
     *
     * @return string
     */
    public function getPaymentAmountTotal()
    {
        return $this->paymentAmountTotal;
    }

    /**
     * Set paymentComissionTotal
     *
     * @param string $paymentComissionTotal
     * @return PaymentReceipt
     */
    public function setPaymentComissionTotal($paymentComissionTotal)
    {
        $this->paymentComissionTotal = $paymentComissionTotal;

        return $this;
    }

    /**
     * Get paymentComissionTotal
     *
     * @return string
     */
    public function getPaymentComissionTotal()
    {
        return $this->paymentComissionTotal;
    }

    /**
     * Set resultAmount
     *
     * @param string $resultAmount
     * @return PaymentReceipt
     */
    public function setResultAmount($resultAmount)
    {
        $this->resultAmount = $resultAmount;

        return $this;
    }

    /**
     * Get resultAmount
     *
     * @return string
     */
    public function getResultAmount()
    {
        return $this->resultAmount;
    }

    /**
     * Set profit
     *
     * @param string $profit
     * @return PaymentReceipt
     */
    public function setProfit($profit)
    {
        $this->profit = $profit;

        return $this;
    }

    /**
     * Get profit
     *
     * @return string
     */
    public function getProfit()
    {
        return $this->profit;
    }

    /**
     * Get checksumHash
     *
     * @return string
     */
    public function getChecksumHash()
    {
        return $this->checksumHash;
    }

    /**
     * Set checksumHash
     *
     * @param string $checksumHash
     * @return PaymentReceipt
     */
    public function setChecksumHash($checksumHash)
    {
        $this->checksumHash = $checksumHash;

        return $this;
    }

    public function generateChecksumHash()
    {
        return hash('sha256',
            $this->getReceiptNumber()
            . $this->getReceiptDate()->format('j/m/Y')
            . $this->getDocumentNumber()
            . $this->getOperationalDate()->format('j/m/Y')
            . $this->getNfcTagNumber()
            . $this->getPayerFullName()
            . $this->getPaymentPurpose()
            . $this->getPaymentAmount()
            . $this->getPaymentComission()
            . $this->getPaymentNumbers()
            . $this->getPaymentAmountTotal()
            . $this->getPaymentComissionTotal()
            . $this->getResultAmount()
        );
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Set nfcTag
     *
     * @param \AppBundle\Entity\NfcTag\NfcTag $nfcTag
     * @return PaymentReceipt
     */
    public function setNfcTag(\AppBundle\Entity\NfcTag\NfcTag $nfcTag = null)
    {
        $this->nfcTag = $nfcTag;

        return $this;
    }

    /**
     * Get nfcTag
     *
     * @return \AppBundle\Entity\NfcTag\NfcTag
     */
    public function getNfcTag()
    {
        return $this->nfcTag;
    }

    /**
     * Set student
     *
     * @param \AppBundle\Entity\Student\Student $student
     * @return PaymentReceipt
     */
    public function setStudent(\AppBundle\Entity\Student\Student $student = null)
    {
        $this->student = $student;

        return $this;
    }

    /**
     * Get student
     *
     * @return \AppBundle\Entity\Student\Student
     */
    public function getStudent()
    {
        return $this->student;
    }
}
