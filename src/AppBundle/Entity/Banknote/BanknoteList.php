<?php
// src/AppBundle/Entity/Banknote/BanknoteList.php
namespace AppBundle\Entity\Banknote;

use Symfony\Component\Validator\Constraints as Assert,
    Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection;

use AppBundle\Entity\Utility\Traits\DoctrineMapping\IdMapperTrait;

/**
 * @ORM\Table(name="banknotes_lists")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Banknote\Repository\BanknoteListRepository")
 */
class BanknoteList
{
    use IdMapperTrait;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="AppBundle\Entity\Transaction\Transaction",
     *     inversedBy="banknoteLists",
     *     cascade={"remove"}
     * )
     * @ORM\JoinColumn(name="transaction_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $transaction;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="AppBundle\Entity\Banknote\Banknote",
     *     inversedBy="banknoteLists",
     *     cascade={"remove"}
     * )
     * @ORM\JoinColumn(name="banknote_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $banknote;

    /**
     * @ORM\Column(type="integer")
     */
    protected $quantity;

    public function __toString()
    {
        return (string)$this->id ?: static::class;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return BanknoteList
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set transaction
     *
     * @param \AppBundle\Entity\Transaction\Transaction $transaction
     *
     * @return BanknoteList
     */
    public function setTransaction(\AppBundle\Entity\Transaction\Transaction $transaction = null)
    {
        $this->transaction = $transaction;

        return $this;
    }

    /**
     * Get transaction
     *
     * @return \AppBundle\Entity\Transaction\Transaction
     */
    public function getTransaction()
    {
        return $this->transaction;
    }

    /**
     * Set banknote
     *
     * @param \AppBundle\Entity\Banknote\Banknote $banknote
     *
     * @return Banknote
     */
    public function setBanknote(\AppBundle\Entity\Banknote\Banknote $banknote = null)
    {
        $this->banknote = $banknote;

        return $this;
    }

    /**
     * Get banknote
     *
     * @return \AppBundle\Entity\Banknote\Banknote
     */
    public function getBanknote()
    {
        return $this->banknote;
    }
}
