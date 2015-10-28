<?php
// AppBundle/Entity/NfcTag/NfcTag.php
namespace AppBundle\Entity\NfcTag;

use Symfony\Component\Validator\Constraints as Assert,
    Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection;

use AppBundle\Entity\Utility\Traits\DoctrineMapping\IdMapperTrait,
    AppBundle\Validator\Constraints as CustomAssert,
    AppBundle\Entity\NfcTag\Utility\Interfaces\SyncNfcTagPropertiesInterface;

/**
 * @ORM\Table(name="nfc_tags")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\NfcTag\Repository\NfcTagRepository")
 *
 * @UniqueEntity(fields="number", message="nfc_tag.number.unique")
 * @UniqueEntity(fields="code", message="nfc_tag.code.unique")
 */
class NfcTag implements SyncNfcTagPropertiesInterface
{
    use IdMapperTrait;

    #/**
    # * @ORM\ManyToOne(targetEntity="AppBundle\Entity\VendingMachine\VendingMachine", inversedBy="nfcTags")
    # * @ORM\JoinColumn(name="vending_machine_id", referencedColumnName="id", onDelete="SET NULL")
    # */
    #protected $vendingMachine;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Student\Student", inversedBy="nfcTag")
     * @ORM\JoinColumn(name="student_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $student;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Purchase\Purchase", mappedBy="nfcTag")
     */
    protected $purchases;

    /**
     * @ORM\Column(type="string", length=8, unique=true)
     *
     * @Assert\NotBlank(message="nfc_tag.number.not_blank")
     * @Assert\Regex(
     *     pattern = "/^[A-Z]{2}[0-9]{6}$/",
     *     message = "nfc_tag.number.regex"
     * )
     */
    protected $number;

    /**
     * @ORM\Column(type="string", length=32, unique=true)
     *
     * @Assert\NotBlank(message="nfc_tag.code.not_blank")
     * @Assert\Length(
     *      min=1,
     *      max=32,
     *      minMessage="nfc_tag.code.length.min",
     *      maxMessage="nfc_tag.code.length.max"
     * )
     */
    protected $code;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->purchases = new ArrayCollection;
    }

    /**
     * Set number
     *
     * @param string $number
     * @return NfcTag
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return NfcTag
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    #/**
    # * Set vendingMachine
    # *
    # * @param \AppBundle\Entity\VendingMachine\VendingMachine $vendingMachine
    # * @return NfcTag
    # */
    #public function setVendingMachine(\AppBundle\Entity\VendingMachine\VendingMachine $vendingMachine = null)
    #{
    #    $this->vendingMachine = $vendingMachine;
    #
    #    return $this;
    #}

    #/**
    # * Get vendingMachine
    # *
    # * @return \AppBundle\Entity\VendingMachine\VendingMachine
    # */
    #public function getVendingMachine()
    #{
    #    return $this->vendingMachine;
    #}

    /**
     * Set student
     *
     * @param \AppBundle\Entity\Student\Student $student
     * @return NfcTag
     */
    public function setStudent(\AppBundle\Entity\Student\Student $student = null)
    {
        $this->student = $student;

        return $this;
    }

    /**
     * Add purchase
     *
     * @param \AppBundle\Entity\Purchase\Purchase $purchase
     * @return NfcTag
     */
    public function addPurchase(\AppBundle\Entity\Purchase\Purchase $purchase)
    {
        $purchase->setNfcTag($this);
        $this->purchases[] = $purchase;

        return $this;
    }

    /**
     * Remove purchases
     *
     * @param \AppBundle\Entity\Purchase\Purchase $purchases
     */
    public function removePurchase(\AppBundle\Entity\Purchase\Purchase $purchases)
    {
        $this->purchases->removeElement($purchases);
    }

    /**
     * Get purchases
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPurchases()
    {
        return $this->purchases;
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

    static public function getSyncArrayName()
    {
        return self::NFC_TAG_ARRAY;
    }

    public function getSyncObjectData()
    {
        return [
            self::NFC_TAG_CODE        => $this->getCode(),
            self::NFC_TAG_DAILY_LIMIT => $this->getStudent()->getDailyLimit(),
            self::NFC_TAG_TOTAL_LIMIT => $this->getStudent()->getTotalLimit()
        ];
    }
}