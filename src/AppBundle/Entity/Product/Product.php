<?php
// AppBundle/Entity/Product/Product.php
namespace AppBundle\Entity\Product;

use DateTime;

use Symfony\Component\Validator\Constraints as Assert,
    Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity,
    Symfony\Component\HttpFoundation\File\File;

use Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection;

use Gedmo\Mapping\Annotation as Gedmo;

use Vich\UploaderBundle\Mapping\Annotation as Vich;

use AppBundle\Entity\Utility\Traits\DoctrineMapping\IdMapperTrait,
    AppBundle\Entity\Utility\Traits\DoctrineMapping\SlugMapper,
    AppBundle\Entity\Utility\Traits\DoctrineMapping\PseudoDeleteMapperTrait,
    AppBundle\Validator\Constraints as CustomAssert,
    AppBundle\Entity\Product\Utility\Interfaces\SyncProductPropertiesInterface;

/**
 * @ORM\Table(name="products")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Product\Repository\ProductRepository")
 *
 * @UniqueEntity(fields="code", message="product.code.unique")
 *
 * @Vich\Uploadable
 */
class Product implements SyncProductPropertiesInterface
{
    use IdMapperTrait, SlugMapper, PseudoDeleteMapperTrait;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Product\ProductCategory", inversedBy="products")
     * @ORM\JoinColumn(name="product_category_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $productCategory;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Product\ProductImage", mappedBy="product", cascade={"persist", "remove"})
     */
    protected $productImages;

    protected $uploadedProductImages;

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
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Student\Student", mappedBy="products")
     */
    protected $students;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Purchase\Purchase", mappedBy="product")
     */
    protected $purchases;

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
     * @ORM\Column(type="string", length=100)
     *
     * @Assert\NotBlank(message="product.name_notification.not_blank")
     * @Assert\Length(
     *      min=2,
     *      max=50,
     *      minMessage="product.name_notification.length.min",
     *      maxMessage="product.name_notification.length.max"
     * )
     */
    protected $nameNotification;

    /**
     * @ORM\Column(length=128, unique=true)
     *
     * @Gedmo\Slug(
     *      fields={"nameShort"},
     *      separator="_",
     *      style="lower"
     * )
     */
    protected $slug;

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
     * @ORM\Column(type="string", length=250)
     *
     * @Assert\NotBlank(message="product.description_short.not_blank")
     * @Assert\Length(
     *      min=5,
     *      max=250,
     *      minMessage="product.description_short.length.min",
     *      maxMessage="product.description_short.length.max"
     * )
     */
    protected $descriptionShort;

    /**
     * @ORM\Column(type="text")
     *
     * @Assert\NotBlank(message="product.description.not_blank")
     * @Assert\Length(
     *      min=5,
     *      max=10000,
     *      minMessage="product.description.length.min",
     *      maxMessage="product.description.length.max"
     * )
     */
    protected $description;

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
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     *
     * @CustomAssert\IsDecimalConstraint
     */
    protected $protein;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     *
     * @CustomAssert\IsDecimalConstraint
     */
    protected $fat;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     *
     * @CustomAssert\IsDecimalConstraint
     */
    protected $carbohydrate;

    /*
     * @ORM\Column(type="integer", nullable=true)
     *
     * @Assert\Range(
     *      min=1,
     *      max=1000,
     *      minMessage="product.calories.range.min",
     *      maxMessage="product.calories.range.max"
     * )
     */

    /**
    * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
    *
    * @CustomAssert\IsDecimalConstraint
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
    protected $weight;

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
        $this->productImages        = new ArrayCollection;
        $this->productVendingGroups = new ArrayCollection;
        $this->students             = new ArrayCollection;
        $this->purchases            = new ArrayCollection;
    }

    public function getSearchProperties()
    {
        $searchProperties = [
            $this->getNameFull(),
            $this->getCode(),
        ];

        if( $this->getProductCategory() ) {
            $searchProperties[] = $this->getProductCategory()->getName();
        }

        if( $this->getSupplier() ) {
            $searchProperties[] = $this->getSupplier()->getName();
        }

        return $searchProperties;
    }

    /* Vich Uploadable Methods */

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
     * Set nameNotification
     *
     * @param string $nameNotification
     * @return Product
     */
    public function setNameNotification($nameNotification)
    {
        $this->nameNotification = $nameNotification;

        return $this;
    }

    /**
     * Get nameNotification
     *
     * @return string
     */
    public function getNameNotification()
    {
        return $this->nameNotification;
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
     * Set descriptionShort
     *
     * @param string $descriptionShort
     * @return Product
     */
    public function setDescriptionShort($descriptionShort)
    {
        $this->descriptionShort = $descriptionShort;

        return $this;
    }

    /**
     * Get descriptionShort
     *
     * @return string
     */
    public function getDescriptionShort()
    {
        return $this->descriptionShort;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Product
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
     * Set protein
     *
     * @param string $protein
     * @return Product
     */
    public function setProtein($protein)
    {
        $this->protein = $protein;

        return $this;
    }

    /**
     * Get protein
     *
     * @return string
     */
    public function getProtein()
    {
        return $this->protein;
    }

    /**
     * Set fat
     *
     * @param string $fat
     * @return Product
     */
    public function setFat($fat)
    {
        $this->fat = $fat;

        return $this;
    }

    /**
     * Get fat
     *
     * @return string
     */
    public function getFat()
    {
        return $this->fat;
    }

    /**
     * Set carbohydrate
     *
     * @param string $carbohydrate
     * @return Product
     */
    public function setCarbohydrate($carbohydrate)
    {
        $this->carbohydrate = $carbohydrate;

        return $this;
    }

    /**
     * Get carbohydrate
     *
     * @return string
     */
    public function getCarbohydrate()
    {
        return $this->carbohydrate;
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
     * Set weight
     *
     * @param integer $weight
     * @return Product
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * Get weight
     *
     * @return integer
     */
    public function getWeight()
    {
        return $this->weight;
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
     * Add productImage
     *
     * @param \AppBundle\Entity\Product\ProductImage $productImage
     * @return Product
     */
    public function addProductImage(\AppBundle\Entity\Product\ProductImage $productImage)
    {
        $productImage->setProduct($this);
        $this->productImages[] = $productImage;

        return $this;
    }

    /**
     * Remove productImages
     *
     * @param \AppBundle\Entity\Product\ProductImage $productImages
     */
    public function removeProductImage(\AppBundle\Entity\Product\ProductImage $productImages)
    {
        $this->productImages->removeElement($productImages);
    }

    /**
     * Get productImages
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProductImages()
    {
        return $this->productImages;
    }

    public function addUploadedProductImage($image)
    {
        $this->uploadedProductImages[] = $image;

        return $this;
    }

    public function removeUploadedProductImage($image)
    {
        $this->uploadedProductImages->removeElement($image);

        return $this;
    }

    public function getUploadedProductImages()
    {
        return $this->uploadedProductImages;
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

    /**
     * Add students
     *
     * @param \AppBundle\Entity\Student\Student $students
     * @return Product
     */
    public function addStudent(\AppBundle\Entity\Student\Student $students)
    {
        $this->students[] = $students;

        return $this;
    }

    /**
     * Remove students
     *
     * @param \AppBundle\Entity\Student\Student $students
     */
    public function removeStudent(\AppBundle\Entity\Student\Student $students)
    {
        $this->students->removeElement($students);
    }

    /**
     * Get students
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStudents()
    {
        return $this->students;
    }

    /**
     * Add purchase
     *
     * @param \AppBundle\Entity\Purchase\Purchase $purchase
     * @return Product
     */
    public function addPurchase(\AppBundle\Entity\Purchase\Purchase $purchase)
    {
        $purchase->setProduct($this);
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

    public function getWeightAndMeasure()
    {
        return ( $this->weight )
            ? "{$this->weight} {$this->measurementUnit}"
            : '-';
    }

    static public function getSyncArrayName()
    {
        return self::PRODUCT_ARRAY;
    }

    public function getSyncObjectData()
    {
        return [
            self::PRODUCT_ID    => $this->getId(),
            self::PRODUCT_NAME  => $this->getNameShort(),
            self::PRODUCT_PRICE => $this->getPrice()
        ];
    }

    static public function getSyncArrayNameRestricted()
    {
        return self::PRODUCT_RESTRICTED_ARRAY;
    }

    public function getSyncObjectDataRestricted()
    {
        return [
            self::PRODUCT_RESTRICTED_ID => $this->getId()
        ];
    }
}
