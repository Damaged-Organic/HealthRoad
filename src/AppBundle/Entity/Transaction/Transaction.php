<?php
// src/AppBundle/Entity/Transaction/Transaction.php
namespace AppBundle\Entity\Transaction;

use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection;

use AppBundle\Entity\Utility\Traits\DoctrineMapping\IdMapperTrait,
    AppBundle\Entity\Transaction\Utility\Interfaces\SyncTransactionPropertiesInterface,
    AppBundle\Validator\Constraints as CustomAssert;

/**
 * @ORM\Table(name="transactions")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Transaction\Repository\TransactionRepository")
 */
class Transaction implements SyncTransactionPropertiesInterface
{
    use IdMapperTrait;

    // TODO: This joined entities will show fuck all if deleted. Need to find the way to preserve info in case of deletion

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\VendingMachine\VendingMachine", inversedBy="transactions")
     * @ORM\JoinColumn(name="vending_machine_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $vendingMachine;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Student\Student", inversedBy="transactions")
     * @ORM\JoinColumn(name="student_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $student;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\NfcTag\NfcTag", inversedBy="transactions")
     * @ORM\JoinColumn(name="nfc_tag_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $nfcTag;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Banknote\BanknoteList", mappedBy="transaction")
     */
    protected $banknoteLists;

    /**
     * @ORM\Column(type="integer")
     */
    protected $syncTransactionId;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $syncTransactionAt;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    protected $syncNfcTagCode;

    /**
     * @ORM\Column(type="bigint", nullable=true)
     */
    protected $syncStudentId;

    /**
     * @ORM\Column(type="string", length=16)
     */
    protected $vendingMachineSerial;

    /**
     * @ORM\Column(type="string", length=64)
     */
    protected $vendingMachineSyncId;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    protected $totalAmount;

    public function __construct()
    {
        $this->banknoteLists = new ArrayCollection;
    }

    public function __toString()
    {
        return (string)$this->transactionId ?: static::class;
    }

    public function getSearchProperties()
    {
        $searchProperties = [];

        if( $this->getSyncTransactionAt() ) {
            $searchProperties[] = $this->getSyncTransactionAt()->format('Y-m-d H:i:s');
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
     * Set id
     *
     * @param integer $id
     * @return Transaction
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set syncTransactionId
     *
     * @param integer $syncTransactionId
     * @return Transaction
     */
    public function setSyncTransactionId($syncTransactionId)
    {
        $this->syncTransactionId = $syncTransactionId;

        return $this;
    }

    /**
     * Get syncTransactionId
     *
     * @return integer
     */
    public function getSyncTransactionId()
    {
        return $this->syncTransactionId;
    }

    /**
     * Set syncTransactionAt
     *
     * @param \DateTime $syncTransactionAt
     * @return Transaction
     */
    public function setSyncTransactionAt($syncTransactionAt)
    {
        $this->syncTransactionAt = $syncTransactionAt;

        return $this;
    }

    /**
     * Get syncTransactionAt
     *
     * @return \DateTime
     */
    public function getSyncTransactionAt()
    {
        return $this->syncTransactionAt;
    }

    /**
     * Set syncNfcTagCode
     *
     * @param string $syncNfcTagCode
     * @return Transaction
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
     * @return Transaction
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
     * Set vendingMachineSerial
     *
     * @param string $vendingMachineSerial
     * @return Transaction
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
     * @return Transaction
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
     * @return Transaction
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
     * Set student
     *
     * @param \AppBundle\Entity\Student\Student $student
     * @return Transaction
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

    /**
     * Set nfcTag
     *
     * @param \AppBundle\Entity\NfcTag\NfcTag $nfcTag
     * @return Transaction
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
     * Add banknoteList
     *
     * @param \AppBundle\Entity\Banknote\BanknoteList $banknoteList
     *
     * @return Transaction
     */
    public function addBanknoteList(\AppBundle\Entity\Banknote\BanknoteList $banknoteList)
    {
        $banknoteList->setTransaction($this);
        $this->banknoteLists[] = $banknoteList;

        return $this;
    }

    /**
     * Remove banknoteList
     *
     * @param \AppBundle\Entity\Banknote\BanknoteList $banknoteList
     */
    public function removeBanknoteList(\AppBundle\Entity\Banknote\BanknoteList $banknoteList)
    {
        $this->banknoteLists->removeElement($banknoteList);
    }

    /**
     * Get banknoteLists
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBanknoteLists()
    {
        return $this->banknoteLists;
    }

    /*-------------------------------------------------------------------------
    | CUSTOM GETTERS
    |------------------------------------------------------------------------*/

    private function getTotalAmountGenerator($banknoteLists)
    {
        foreach($banknoteLists as $banknoteList)
        {
            if( $banknoteList->getQuantity() && $banknoteList->getBanknote() )
            {
                yield bcmul(
                    $banknoteList->getQuantity(), $banknoteList->getBanknote()->getNominal(), 2
                );
            }
        }
    }

    public function setTotalAmount()
    {
        if( !$this->getBanknoteLists() )
            return FALSE;

        $totalAmount = 0;
        foreach( $this->getTotalAmountGenerator($this->getBanknoteLists()) as $value )
        {
            $totalAmount = bcadd($totalAmount, $value, 2);
        }

        $this->totalAmount = $totalAmount;

        return $this;
    }

    public function getTotalAmount()
    {
        return $this->totalAmount;
    }

    /*-------------------------------------------------------------------------
    | SYNCHRONIZATION
    |------------------------------------------------------------------------*/

    static public function getSyncArrayName()
    {
        return self::TRANSACTION_ARRAY;
    }
}
