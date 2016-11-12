<?php
// src/AppBundle/Entity/Purchase/Purchase.php
namespace AppBundle\Entity\Purchase;

use Doctrine\ORM\Mapping as ORM;

use AppBundle\Entity\Utility\Traits\DoctrineMapping\IdMapperTrait,
    AppBundle\Entity\Purchase\Utility\Interfaces\SyncPurchasePropertiesInterface;

/**
 * @ORM\Table(name="purchases")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Purchase\Repository\PurchaseRepository")
 */
class Purchase implements SyncPurchasePropertiesInterface
{
    use IdMapperTrait;

    // TODO: This joined entities will show fuck all if deleted. Need to find the way to preserve info in case of deletion

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\VendingMachine\VendingMachine", inversedBy="purchases")
     * @ORM\JoinColumn(name="vending_machine_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $vendingMachine;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Product\Product", inversedBy="purchases")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */
    protected $product;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\NfcTag\NfcTag", inversedBy="purchases")
     * @ORM\JoinColumn(name="nfc_tag_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $nfcTag;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Student\Student", inversedBy="purchases")
     * @ORM\JoinColumn(name="student_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $student;

    /**
     * @ORM\Column(type="integer")
     */
    protected $syncPurchaseId;

    /**
     * @ORM\Column(type="string", length=32)
     */
    protected $syncNfcTagCode;

    /**
     * @ORM\Column(type="bigint", nullable=true)
     */
    protected $syncStudentId;

    /**
     * @ORM\Column(type="integer")
     */
    protected $syncProductId;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    protected $syncProductPrice;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $syncPurchasedAt;

    /**
     * @ORM\Column(type="string", length=16)
     */
    protected $vendingMachineSerial;

    /**
     * @ORM\Column(type="string", length=64)
     */
    protected $vendingMachineSyncId;

    public function getSearchProperties()
    {
        $searchProperties = [];

        if( $this->getSyncPurchasedAt() ) {
            $searchProperties[] = $this->getSyncPurchasedAt()->format('Y-m-d H:i:s');
        }

        if( $this->getProduct() ) {
            $searchProperties[] = $this->getProduct()->getNameFull();
        }

        if( $this->getVendingMachine() ) {
            $searchProperties[] = $this->getVendingMachine()->getSerial();
        }

        if( $this->getNfcTag() ) {
            $searchProperties[] = $this->getNfcTag()->getNumber();
        }

        if( $this->getStudent() ) {
            $searchProperties[] = $this->getStudent()->getName();
            $searchProperties[] = $this->getStudent()->getSurname();
            $searchProperties[] = $this->getStudent()->getPatronymic();
        }

        return $searchProperties;
    }

    /**
     * Set syncPurchaseId
     *
     * @param integer $syncPurchaseId
     * @return Purchase
     */
    public function setSyncPurchaseId($syncPurchaseId)
    {
        $this->syncPurchaseId = $syncPurchaseId;

        return $this;
    }

    /**
     * Get syncPurchaseId
     *
     * @return integer
     */
    public function getSyncPurchaseId()
    {
        return $this->syncPurchaseId;
    }

    /**
     * Set syncNfcTagCode
     *
     * @param string $syncNfcTagCode
     * @return Purchase
     */
    public function setSyncNfcTagCode($syncNfcTagCode)
    {
        $this->syncNfcTagCode = $syncNfcTagCode;

        return $this;
    }

    /**
     * Get syncNfcTagCode
     *
     * @return string
     */
    public function getSyncNfcTagCode()
    {
        return $this->syncNfcTagCode;
    }

    /**
     * Set syncStudentId
     *
     * @param integer $syncStudentId
     * @return Purchase
     */
    public function setSyncStudentId($syncStudentId)
    {
        $this->syncStudentId = $syncStudentId;

        return $this;
    }

    /**
     * Get syncStudentId
     *
     * @return integer
     */
    public function getSyncStudentId()
    {
        return $this->syncStudentId;
    }

    /**
     * Set syncProductId
     *
     * @param integer $syncProductId
     * @return Purchase
     */
    public function setSyncProductId($syncProductId)
    {
        $this->syncProductId = $syncProductId;

        return $this;
    }

    /**
     * Get syncProductId
     *
     * @return integer
     */
    public function getSyncProductId()
    {
        return $this->syncProductId;
    }

    /**
     * Set syncProductPrice
     *
     * @param string $syncProductPrice
     * @return Purchase
     */
    public function setSyncProductPrice($syncProductPrice)
    {
        $this->syncProductPrice = $syncProductPrice;

        return $this;
    }

    /**
     * Get syncProductPrice
     *
     * @return string
     */
    public function getSyncProductPrice()
    {
        return $this->syncProductPrice;
    }

    /**
     * Set syncPurchasedAt
     *
     * @param \DateTime $syncPurchasedAt
     * @return Purchase
     */
    public function setSyncPurchasedAt($syncPurchasedAt)
    {
        $this->syncPurchasedAt = $syncPurchasedAt;

        return $this;
    }

    /**
     * Get syncPurchasedAt
     *
     * @return \DateTime
     */
    public function getSyncPurchasedAt()
    {
        return $this->syncPurchasedAt;
    }

    /**
     * Set vendingMachineSerial
     *
     * @param string $vendingMachineSerial
     * @return Purchase
     */
    public function setVendingMachineSerial($vendingMachineSerial)
    {
        $this->vendingMachineSerial = $vendingMachineSerial;

        return $this;
    }

    /**
     * Get vendingMachineSerial
     *
     * @return string
     */
    public function getVendingMachineSerial()
    {
        return $this->vendingMachineSerial;
    }

    /**
     * Set vendingMachineSyncId
     *
     * @param string $vendingMachineSyncId
     * @return Purchase
     */
    public function setVendingMachineSyncId($vendingMachineSyncId)
    {
        $this->vendingMachineSyncId = $vendingMachineSyncId;

        return $this;
    }

    /**
     * Get vendingMachineSyncId
     *
     * @return string
     */
    public function getVendingMachineSyncId()
    {
        return $this->vendingMachineSyncId;
    }

    /**
     * Set vendingMachine
     *
     * @param \AppBundle\Entity\VendingMachine\VendingMachine $vendingMachine
     * @return Purchase
     */
    public function setVendingMachine(\AppBundle\Entity\VendingMachine\VendingMachine $vendingMachine = null)
    {
        $this->vendingMachine = $vendingMachine;

        return $this;
    }

    /**
     * Get vendingMachine
     *
     * @return \AppBundle\Entity\VendingMachine\VendingMachine
     */
    public function getVendingMachine()
    {
        return $this->vendingMachine;
    }

    /**
     * Set product
     *
     * @param \AppBundle\Entity\Product\Product $product
     * @return Purchase
     */
    public function setProduct(\AppBundle\Entity\Product\Product $product = null)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return \AppBundle\Entity\Product\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set nfcTag
     *
     * @param \AppBundle\Entity\NfcTag\NfcTag $nfcTag
     * @return Purchase
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
     * @return Purchase
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

    /*-------------------------------------------------------------------------
    | SYNCHRONIZATION
    |------------------------------------------------------------------------*/

    static public function getSyncArrayName()
    {
        return self::PURCHASE_ARRAY;
    }
}
