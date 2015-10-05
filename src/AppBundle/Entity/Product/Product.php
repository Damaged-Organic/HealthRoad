<?php
// AppBundle/Entity/Product/Product.php
namespace AppBundle\Entity\Product;

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
 * @ORM\Table(name="products")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Product\Repository\ProductRepository")
 *
 * @UniqueEntity(fields="code", message="product.code.unique")
 *
 * @Vich\Uploadable
 */
class Product
{
    use IdMapperTrait;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Product\ProductCategory", inversedBy="products")
     * @ORM\JoinColumn(name="product_category_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $productCategory;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Supplier\Supplier", inversedBy="products")
     * @ORM\JoinColumn(name="supplier_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $supplier;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Product\ProductVendingGroup", mappedBy="products")
     */
    protected $productVendingGroups;

    /**
     * @ORM\Column(type="string", length=250)
     *
     * @Assert\NotBlank(message="product.name_full.not_blank")
     * @Assert\Length(
     *      min=2,
     *      max=250,
     *      minMessage="product.name_full.length.min",
     *      maxMessage="product.name_full.length.max"
     * )
     */
    protected $nameFull;

    /**
     * @ORM\Column(type="string", length=100)
     *
     * @Assert\NotBlank(message="product.name_short.not_blank")
     * @Assert\Length(
     *      min=2,
     *      max=100,
     *      minMessage="product.name_short.length.min",
     *      maxMessage="product.name_short.length.max"
     * )
     */
    protected $nameShort;

    /**
     * @ORM\Column(type="string", length=100, unique=true)
     *
     * @Assert\NotBlank(message="product.code.not_blank")
     * @Assert\Length(
     *      min=2,
     *      max=100,
     *      minMessage="product.code.length.min",
     *      maxMessage="product.code.length.max"
     * )
     */
    protected $code;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     *
     * @CustomAssert\IsPriceConstraint
     */
    protected $price;

    /**
     * @Assert\File(
     *     maxSize="2M",
     *     mimeTypes={"image/png", "image/jpeg", "image/pjpeg"}
     * )
     *
     * @Vich\UploadableField(mapping="product_image", fileNameProperty="imageProductName")
     */
    protected $imageProductFile;

    /**
     * @ORM\Column(type="string", length=250, nullable=true)
     */
    protected $imageProductName;

    /**
     * @Assert\File(
     *     maxSize="2M",
     *     mimeTypes={"image/png", "image/jpeg", "image/pjpeg"}
     * )
     *
     * @Vich\UploadableField(mapping="product_certificate", fileNameProperty="imageCertificateName")
     */
    protected $imageCertificateFile;

    /**
     * @ORM\Column(type="string", length=250, nullable=true)
     */
    protected $imageCertificateName;

    /**
     * @ORM\Column(type="string", length=250, nullable=true)
     *
     * @Assert\Length(
     *      min=2,
     *      max=250,
     *      minMessage="product.manufacturer.length.min",
     *      maxMessage="product.manufacturer.length.max"
     * )
     */
    protected $manufacturer;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @Assert\Range(
     *      min=1,
     *      max=1000,
     *      minMessage="product.calories.length.min",
     *      maxMessage="product.calories.length.max"
     * )
     */
    protected $calories;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     *
     * @Assert\Length(
     *      min=1,
     *      max=200,
     *      minMessage="product.shelf_life.length.min",
     *      maxMessage="product.shelf_life.length.max"
     * )
     */
    protected $shelfLife;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @Assert\Range(
     *      min=-100,
     *      max=100,
     *      minMessage="product.storage_temperature.length.min",
     *      maxMessage="product.storage_temperature.length.max"
     * )
     */
    protected $storageTemperatureMin;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @Assert\Range(
     *      min=-100,
     *      max=100,
     *      minMessage="product.storage_temperature.length.min",
     *      maxMessage="product.storage_temperature.length.max"
     * )
     */
    protected $storageTemperatureMax;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @Assert\Range(
     *      min=1,
     *      max=10000,
     *      minMessage="product.weight.length.min",
     *      maxMessage="product.weight.length.max"
     * )
     */
    protected $weigth;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     *
     * @Assert\Length(
     *      min=1,
     *      max=50,
     *      minMessage="product.measurement_unit.length.min",
     *      maxMessage="product.measurement_unit.length.max"
     * )
     */
    protected $measurementUnit;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @Assert\Range(
     *      min=1,
     *      max=10000,
     *      minMessage="product.amount_in_box.length.min",
     *      maxMessage="product.amount_in_box.length.max"
     * )
     */
    protected $amountInBox;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $updatedAt;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->productVendingGroups = new ArrayCollection;
    }

    /* Vich Uploadable Methods */

    public function setImageProductFile(File $imageProduct = NULL)
    {
        $this->imageProductFile = $imageProduct;

        if( $imageProduct )
            $this->updatedAt = new DateTime('now');
    }

    public function getImageProductFile()
    {
        return $this->imageProductFile;
    }

    public function setImageCertificateFile(File $imageCertificate = NULL)
    {
        $this->imageCertificateFile = $imageCertificate;

        if( $imageCertificate )
            $this->updatedAt = new DateTime('now');
    }

    public function getImageCertificateFile()
    {
        return $this->imageCertificateFile;
    }

    /* End \ Vich Uploadable Methods */

    /**
     * Set nameFull
     *
     * @param string $nameFull
     * @return Product
     */
    public function setNameFull($nameFull)
    {
        $this->nameFull = $nameFull;

        return $this;
    }

    /**
     * Get nameFull
     *
     * @return string 
     */
    public function getNameFull()
    {
        return $this->nameFull;
    }

    /**
     * Set nameShort
     *
     * @param string $nameShort
     * @return Product
     */
    public function setNameShort($nameShort)
    {
        $this->nameShort = $nameShort;

        return $this;
    }

    /**
     * Get nameShort
     *
     * @return string 
     */
    public function getNameShort()
    {
        return $this->nameShort;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return Product
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
     * Set price
     *
     * @param string $price
     * @return Product
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string 
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set imageProductName
     *
     * @param string $imageProductName
     * @return Product
     */
    public function setImageProductName($imageProductName)
    {
        $this->imageProductName = $imageProductName;

        return $this;
    }

    /**
     * Get imageProductName
     *
     * @return string 
     */
    public function getImageProductName()
    {
        return $this->imageProductName;
    }

    /**
     * Set imageCertificateName
     *
     * @param string $imageCertificateName
     * @return Product
     */
    public function setImageCertificateName($imageCertificateName)
    {
        $this->imageCertificateName = $imageCertificateName;

        return $this;
    }

    /**
     * Get imageCertificateName
     *
     * @return string 
     */
    public function getImageCertificateName()
    {
        return $this->imageCertificateName;
    }

    /**
     * Set manufacturer
     *
     * @param string $manufacturer
     * @return Product
     */
    public function setManufacturer($manufacturer)
    {
        $this->manufacturer = $manufacturer;

        return $this;
    }

    /**
     * Get manufacturer
     *
     * @return string 
     */
    public function getManufacturer()
    {
        return $this->manufacturer;
    }

    /**
     * Set calories
     *
     * @param integer $calories
     * @return Product
     */
    public function setCalories($calories)
    {
        $this->calories = $calories;

        return $this;
    }

    /**
     * Get calories
     *
     * @return integer 
     */
    public function getCalories()
    {
        return $this->calories;
    }

    /**
     * Set shelfLife
     *
     * @param string $shelfLife
     * @return Product
     */
    public function setShelfLife($shelfLife)
    {
        $this->shelfLife = $shelfLife;

        return $this;
    }

    /**
     * Get shelfLife
     *
     * @return string 
     */
    public function getShelfLife()
    {
        return $this->shelfLife;
    }

    /**
     * Set storageTemperatureMin
     *
     * @param integer $storageTemperatureMin
     * @return Product
     */
    public function setStorageTemperatureMin($storageTemperatureMin)
    {
        $this->storageTemperatureMin = $storageTemperatureMin;

        return $this;
    }

    /**
     * Get storageTemperatureMin
     *
     * @return integer 
     */
    public function getStorageTemperatureMin()
    {
        return $this->storageTemperatureMin;
    }

    /**
     * Set storageTemperatureMax
     *
     * @param integer $storageTemperatureMax
     * @return Product
     */
    public function setStorageTemperatureMax($storageTemperatureMax)
    {
        $this->storageTemperatureMax = $storageTemperatureMax;

        return $this;
    }

    /**
     * Get storageTemperatureMax
     *
     * @return integer
     */
    public function getStorageTemperatureMax()
    {
        return $this->storageTemperatureMax;
    }

    /**
     * Set weigth
     *
     * @param integer $weigth
     * @return Product
     */
    public function setWeigth($weigth)
    {
        $this->weigth = $weigth;

        return $this;
    }

    /**
     * Get weigth
     *
     * @return integer 
     */
    public function getWeigth()
    {
        return $this->weigth;
    }

    /**
     * Set measurementUnit
     *
     * @param string $measurementUnit
     * @return Product
     */
    public function setMeasurementUnit($measurementUnit)
    {
        $this->measurementUnit = $measurementUnit;

        return $this;
    }

    /**
     * Get measurementUnit
     *
     * @return string 
     */
    public function getMeasurementUnit()
    {
        return $this->measurementUnit;
    }

    /**
     * Set amountInBox
     *
     * @param integer $amountInBox
     * @return Product
     */
    public function setAmountInBox($amountInBox)
    {
        $this->amountInBox = $amountInBox;

        return $this;
    }

    /**
     * Get amountInBox
     *
     * @return integer 
     */
    public function getAmountInBox()
    {
        return $this->amountInBox;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Product
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
     * Set productCategory
     *
     * @param \AppBundle\Entity\Product\ProductCategory $productCategory
     * @return Product
     */
    public function setProductCategory(\AppBundle\Entity\Product\ProductCategory $productCategory = null)
    {
        $this->productCategory = $productCategory;

        return $this;
    }

    /**
     * Get productCategory
     *
     * @return \AppBundle\Entity\Product\ProductCategory 
     */
    public function getProductCategory()
    {
        return $this->productCategory;
    }

    /**
     * Set supplier
     *
     * @param \AppBundle\Entity\Supplier\Supplier $supplier
     * @return Product
     */
    public function setSupplier(\AppBundle\Entity\Supplier\Supplier $supplier = null)
    {
        $this->supplier = $supplier;

        return $this;
    }

    /**
     * Get supplier
     *
     * @return \AppBundle\Entity\Supplier\Supplier 
     */
    public function getSupplier()
    {
        return $this->supplier;
    }

    /**
     * Add productVendingGroups
     *
     * @param \AppBundle\Entity\Product\ProductVendingGroup $productVendingGroups
     * @return Product
     */
    public function addProductVendingGroup(\AppBundle\Entity\Product\ProductVendingGroup $productVendingGroups)
    {
        $this->productVendingGroups[] = $productVendingGroups;

        return $this;
    }

    /**
     * Remove productVendingGroups
     *
     * @param \AppBundle\Entity\Product\ProductVendingGroup $productVendingGroups
     */
    public function removeProductVendingGroup(\AppBundle\Entity\Product\ProductVendingGroup $productVendingGroups)
    {
        $this->productVendingGroups->removeElement($productVendingGroups);
    }

    /**
     * Get productVendingGroups
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProductVendingGroups()
    {
        return $this->productVendingGroups;
    }
}