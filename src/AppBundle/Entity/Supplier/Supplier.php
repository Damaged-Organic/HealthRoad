<?php
// AppBundle/Entity/Supplier/Supplier.php
namespace AppBundle\Entity\Supplier;

use DateTime;

use Symfony\Component\Validator\Constraints as Assert,
    Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity,
    Symfony\Component\HttpFoundation\File\File;

use Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection;

use Vich\UploaderBundle\Mapping\Annotation as Vich;

use AppBundle\Entity\Utility\Traits\DoctrineMapping\IdMapperTrait,
    AppBundle\Validator\Constraints as CustomAssert;

/**
 * @ORM\Table(name="suppliers")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Supplier\Repository\SupplierRepository")
 *
 * @UniqueEntity(fields="name", message="supplier.name.unique")
 *
 * @Vich\Uploadable
 */
class Supplier
{
    use IdMapperTrait;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Product\Product", mappedBy="supplier")
     */
    protected $products;

    /**
     * @ORM\Column(type="string", length=250, unique=true)
     *
     * @Assert\NotBlank(message="supplier.name.not_blank")
     * @Assert\Length(
     *      min=2,
     *      max=250,
     *      minMessage="supplier.name.length.min",
     *      maxMessage="supplier.name.length.max"
     * )
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=500)
     *
     * @Assert\NotBlank(message="supplier.name_legal.not_blank")
     * @Assert\Length(
     *      min=2,
     *      max=500,
     *      minMessage="supplier.name_legal.length.min",
     *      maxMessage="supplier.name_legal.length.max"
     * )
     */
    protected $nameLegal;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @Assert\File(
     *     maxSize="1M",
     *     mimeTypes={"image/png", "image/jpeg", "image/pjpeg", "image/gif"}
     * )
     *
     * @Vich\UploadableField(mapping="supplier_logo", fileNameProperty="logoName")
     */
    protected $logoFile;

    /**
     * @ORM\Column(type="string", length=250, nullable=true)
     */
    protected $logoName;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     *
     * @CustomAssert\IsPhoneNumberConstraint
     */
    protected $phoneNumberSupplier;

    /**
     * @ORM\Column(type="string", length=60, nullable=true)
     *
     * @Assert\Email(
     *      message="common.email.valid",
     *      checkMX=true
     * )
     */
    protected $emailSupplier;

    /**
     * @ORM\Column(type="string", length=250, nullable=true)
     *
     * @Assert\Length(
     *      min=2,
     *      max=250,
     *      minMessage="supplier.name_contact.length.min",
     *      maxMessage="supplier.name_contact.length.max"
     * )
     * @Assert\Regex(
     *     pattern="/^[a-zA-Z\p{L}-\s]+$/u",
     *     message="common.human_name.regex"
     * )
     */
    protected $nameContact;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     *
     * @CustomAssert\IsPhoneNumberConstraint
     */
    protected $phoneNumberContact;

    /**
     * @ORM\Column(type="string", length=60, nullable=true)
     *
     * @Assert\Email(
     *      message="common.email.valid",
     *      checkMX=true
     * )
     */
    protected $emailContact;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     *
     * @Assert\Length(
     *      min=2,
     *      max=100,
     *      minMessage="supplier.contract_number.length.min",
     *      maxMessage="supplier.contract_number.length.max"
     * )
     */
    protected $contractNumber;

    /**
     * @ORM\Column(type="date", nullable=true)
     *
     * @Assert\Date(
     *      message="common.date.valid"
     * )
     */
    protected $contractDateStart;

    /**
     * @ORM\Column(type="date", nullable=true)
     *
     * @Assert\Date(
     *      message="common.date.valid"
     * )
     */
    protected $contractDateEnd;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $updatedAt;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->products = new ArrayCollection;
    }

    /* Vich Uploadable Methods */

    public function setLogoFile(File $logo = NULL)
    {
        $this->logoFile = $logo;

        if( $logo )
            $this->updatedAt = new DateTime('now');
    }

    public function getLogoFile()
    {
        return $this->logoFile;
    }

    /* End \ Vich Uploadable Methods */

    /**
     * Set name
     *
     * @param string $name
     * @return Supplier
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
     * Set nameLegal
     *
     * @param string $nameLegal
     * @return Supplier
     */
    public function setNameLegal($nameLegal)
    {
        $this->nameLegal = $nameLegal;

        return $this;
    }

    /**
     * Get nameLegal
     *
     * @return string 
     */
    public function getNameLegal()
    {
        return $this->nameLegal;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Supplier
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set logoName
     *
     * @param string $logoName
     * @return Supplier
     */
    public function setLogoName($logoName)
    {
        $this->logoName = $logoName;

        return $this;
    }

    /**
     * Get logoName
     *
     * @return string 
     */
    public function getLogoName()
    {
        return $this->logoName;
    }

    /**
     * Set phoneNumberSupplier
     *
     * @param string $phoneNumberSupplier
     * @return Supplier
     */
    public function setPhoneNumberSupplier($phoneNumberSupplier)
    {
        $this->phoneNumberSupplier = $phoneNumberSupplier;

        return $this;
    }

    /**
     * Get phoneNumberSupplier
     *
     * @return string 
     */
    public function getPhoneNumberSupplier()
    {
        return $this->phoneNumberSupplier;
    }

    /**
     * Set emailSupplier
     *
     * @param string $emailSupplier
     * @return Supplier
     */
    public function setEmailSupplier($emailSupplier)
    {
        $this->emailSupplier = $emailSupplier;

        return $this;
    }

    /**
     * Get emailSupplier
     *
     * @return string 
     */
    public function getEmailSupplier()
    {
        return $this->emailSupplier;
    }

    /**
     * Set nameContact
     *
     * @param string $nameContact
     * @return Supplier
     */
    public function setNameContact($nameContact)
    {
        $this->nameContact = $nameContact;

        return $this;
    }

    /**
     * Get nameContact
     *
     * @return string 
     */
    public function getNameContact()
    {
        return $this->nameContact;
    }

    /**
     * Set phoneNumberContact
     *
     * @param string $phoneNumberContact
     * @return Supplier
     */
    public function setPhoneNumberContact($phoneNumberContact)
    {
        $this->phoneNumberContact = $phoneNumberContact;

        return $this;
    }

    /**
     * Get phoneNumberContact
     *
     * @return string 
     */
    public function getPhoneNumberContact()
    {
        return $this->phoneNumberContact;
    }

    /**
     * Set emailContact
     *
     * @param string $emailContact
     * @return Supplier
     */
    public function setEmailContact($emailContact)
    {
        $this->emailContact = $emailContact;

        return $this;
    }

    /**
     * Get emailContact
     *
     * @return string 
     */
    public function getEmailContact()
    {
        return $this->emailContact;
    }

    /**
     * Set contractNumber
     *
     * @param string $contractNumber
     * @return Supplier
     */
    public function setContractNumber($contractNumber)
    {
        $this->contractNumber = $contractNumber;

        return $this;
    }

    /**
     * Get contractNumber
     *
     * @return string 
     */
    public function getContractNumber()
    {
        return $this->contractNumber;
    }

    /**
     * Set contractDateStart
     *
     * @param \DateTime $contractDateStart
     * @return Supplier
     */
    public function setContractDateStart($contractDateStart)
    {
        $this->contractDateStart = $contractDateStart;

        return $this;
    }

    /**
     * Get contractDateStart
     *
     * @return \DateTime 
     */
    public function getContractDateStart()
    {
        return $this->contractDateStart;
    }

    /**
     * Set contractDateEnd
     *
     * @param \DateTime $contractDateEnd
     * @return Supplier
     */
    public function setContractDateEnd($contractDateEnd)
    {
        $this->contractDateEnd = $contractDateEnd;

        return $this;
    }

    /**
     * Get contractDateEnd
     *
     * @return \DateTime 
     */
    public function getContractDateEnd()
    {
        return $this->contractDateEnd;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Supplier
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Add products
     *
     * @param \AppBundle\Entity\Product\Product $product
     * @return Supplier
     */
    public function addProduct(\AppBundle\Entity\Product\Product $product)
    {
        $product->setSupplier($this);
        $this->products[] = $product;

        return $this;
    }

    /**
     * Remove products
     *
     * @param \AppBundle\Entity\Product\Product $products
     */
    public function removeProduct(\AppBundle\Entity\Product\Product $products)
    {
        $this->products->removeElement($products);
    }

    /**
     * Get products
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProducts()
    {
        return $this->products;
    }
}