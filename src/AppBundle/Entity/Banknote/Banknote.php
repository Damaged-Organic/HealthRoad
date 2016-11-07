<?php
// src/AppBundle/Entity/Banknote/Banknote.php
namespace AppBundle\Entity\Banknote;

use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection;

use AppBundle\Entity\Utility\Traits\DoctrineMapping\IdMapperTrait,
    AppBundle\Validator\Constraints as CustomAssert;

use AppBundle\Entity\Banknote\Utility\Interfaces\BanknoteCurrencyListInterface;

/**
 * @ORM\Table(name="banknotes")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Banknote\Repository\BanknoteRepository")
 */
class Banknote implements BanknoteCurrencyListInterface
{
    use IdMapperTrait;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Banknote\BanknoteList", mappedBy="banknote")
     */
    protected $banknoteLists;

    /**
     * @ORM\Column(type="string", length=3)
     */
    protected $currency;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    protected $nominal;

    public function __construct()
    {
        $this->banknoteLists = new ArrayCollection;
    }

    public function __toString()
    {
        return (string)$this->id ?: static::class;
    }

    /**
     * Set currency
     *
     * @param string $currency
     *
     * @return Banknote
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get currency
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set nominal
     *
     * @param string $nominal
     *
     * @return Banknote
     */
    public function setNominal($nominal)
    {
        $this->nominal = $nominal;

        return $this;
    }

    /**
     * Get nominal
     *
     * @return string
     */
    public function getNominal()
    {
        return $this->nominal;
    }

    /**
     * Add banknoteList
     *
     * @param \AppBundle\Entity\Banknote\BanknoteList $banknoteList
     *
     * @return Banknote
     */
    public function addBanknoteList(\AppBundle\Entity\Banknote\BanknoteList $banknoteList)
    {
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
    | INTERFACE IMPLEMENTATION
    |------------------------------------------------------------------------*/

    static public function getBanknoteCurrencyList()
    {
        return [self::BANKNOTE_CURRENCY_UAH];
    }
}
