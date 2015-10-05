<?php
// AppBundle/Entity/VendingMachine/VendingMachine.php
namespace AppBundle\Entity\VendingMachine;

use Symfony\Component\Validator\Constraints as Assert,
    Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection;

use AppBundle\Entity\Utility\Traits\DoctrineMapping\IdMapperTrait;

/**
 * @ORM\Table(name="vending_machines")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\VendingMachine\Repository\VendingMachineRepository")
 *
 * @UniqueEntity(fields="name", message="vending_machine.name.unique")
 * @UniqueEntity(fields="code", message="vending_machine.code.unique")
 */
class VendingMachine
{
    use IdMapperTrait;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\School\School", inversedBy="vendingMachines")
     * @ORM\JoinColumn(name="school_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $school;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Product\ProductVendingGroup", inversedBy="vendingMachines")
     * @ORM\JoinColumn(name="product_vending_group_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $productVendingGroup;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\NfcTag\NfcTag", mappedBy="vendingMachine")
     */
    protected $nfcTags;

    /**
     * @ORM\Column(type="string", length=250, nullable=true, unique=true)
     *
     * @Assert\Length(
     *      min=2,
     *      max=250,
     *      minMessage="vending_machine.name.length.min",
     *      maxMessage="vending_machine.name.length.max"
     * )
     * @Assert\Regex(
     *     pattern="/^[a-zA-Z0-9\p{L}-\s]+$/u",
     *     message="vending_machine.name.regex"
     * )
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=100, unique=true)
     *
     * @Assert\NotBlank(message="vending_machine.code.not_blank")
     *
     * @Assert\Length(
     *      min=2,
     *      max=100,
     *      minMessage="vending_machine.code.length.min",
     *      maxMessage="vending_machine.code.length.max"
     * )
     */
    protected $code;

    /**
     * @ORM\Column(type="string", length=250, nullable=true)
     *
     * @Assert\Length(
     *      min=2,
     *      max=250,
     *      minMessage="vending_machine.name_technician.length.min",
     *      maxMessage="vending_machine.name_technician.length.max"
     * )
     * @Assert\Regex(
     *     pattern="/^[a-zA-Z\p{L}-\s]+$/u",
     *     message="common.human_name.regex"
     * )
     */
    protected $nameTechnician;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @Assert\Range(
     *      min=1,
     *      max=100,
     *      minMessage="vending_machine.number_shelves.range.min",
     *      maxMessage="vending_machine.number_shelves.range.max"
     * )
     */
    protected $numberShelves;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @Assert\Range(
     *      min=1,
     *      max=1000,
     *      minMessage="vending_machine.number_springs.range.min",
     *      maxMessage="vending_machine.number_springs.range.max"
     * )
     */
    protected $numberSprings;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->nfcTags = new ArrayCollection;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return VendingMachine
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return VendingMachine
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

    /**
     * Set nameTechnician
     *
     * @param string $nameTechnician
     * @return VendingMachine
     */
    public function setNameTechnician($nameTechnician)
    {
        $this->nameTechnician = $nameTechnician;

        return $this;
    }

    /**
     * Get nameTechnician
     *
     * @return string
     */
    public function getNameTechnician()
    {
        return $this->nameTechnician;
    }

    /**
     * Set numberShelves
     *
     * @param integer $numberShelves
     * @return VendingMachine
     */
    public function setNumberShelves($numberShelves)
    {
        $this->numberShelves = $numberShelves;

        return $this;
    }

    /**
     * Get numberShelves
     *
     * @return integer
     */
    public function getNumberShelves()
    {
        return $this->numberShelves;
    }

    /**
     * Set numberSprings
     *
     * @param integer $numberSprings
     * @return VendingMachine
     */
    public function setNumberSprings($numberSprings)
    {
        $this->numberSprings = $numberSprings;

        return $this;
    }

    /**
     * Get numberSprings
     *
     * @return integer
     */
    public function getNumberSprings()
    {
        return $this->numberSprings;
    }

    /**
     * Set school
     *
     * @param \AppBundle\Entity\School\School $school
     * @return VendingMachine
     */
    public function setSchool(\AppBundle\Entity\School\School $school = null)
    {
        $this->school = $school;

        return $this;
    }

    /**
     * Get school
     *
     * @return \AppBundle\Entity\School\School
     */
    public function getSchool()
    {
        return $this->school;
    }

    /**
     * Set productVendingGroup
     *
     * @param \AppBundle\Entity\Product\ProductVendingGroup $productVendingGroup
     * @return VendingMachine
     */
    public function setProductVendingGroup(\AppBundle\Entity\Product\ProductVendingGroup $productVendingGroup = null)
    {
        $this->productVendingGroup = $productVendingGroup;

        return $this;
    }

    /**
     * Get productVendingGroup
     *
     * @return \AppBundle\Entity\Product\ProductVendingGroup
     */
    public function getProductVendingGroup()
    {
        return $this->productVendingGroup;
    }

    /**
     * Add nfcTag
     *
     * @param \AppBundle\Entity\NfcTag\NfcTag $nfcTag
     * @return VendingMachine
     */
    public function addNfcTag(\AppBundle\Entity\NfcTag\NfcTag $nfcTag)
    {
        $nfcTag->setVendingMachine($this);
        $this->nfcTags[] = $nfcTag;

        return $this;
    }

    /**
     * Remove nfcTags
     *
     * @param \AppBundle\Entity\NfcTag\NfcTag $nfcTags
     */
    public function removeNfcTag(\AppBundle\Entity\NfcTag\NfcTag $nfcTags)
    {
        $this->nfcTags->removeElement($nfcTags);
    }

    /**
     * Get nfcTags
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNfcTags()
    {
        return $this->nfcTags;
    }
}