<?php
// AppBundle/Entity/Product/ProductImage.php
namespace AppBundle\Entity\Product;

use DateTime;

use Symfony\Component\Validator\Constraints as Assert,
    Symfony\Component\HttpFoundation\File\File;

use Doctrine\ORM\Mapping as ORM;

use Vich\UploaderBundle\Mapping\Annotation as Vich;

use AppBundle\Entity\Utility\Traits\DoctrineMapping\IdMapperTrait;

/**
 * @ORM\Table(name="products_images")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Product\Repository\ProductImageRepository")
 *
 * @Vich\Uploadable
 */
class ProductImage
{
    use IdMapperTrait;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Product\Product", inversedBy="productImages")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    protected $product;

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
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $updatedAt;

    /* Vich Uploadable Methods */

    public function setImageProductFile(File $imageProduct = NULL)
    {
        $this->imageProductFile = $imageProduct;

        if( $imageProduct )
            $this->updatedAt = new DateTime('now');

        return $this;
    }

    public function getImageProductFile()
    {
        return $this->imageProductFile;
    }

    /* End \ Vich Uploadable Methods */

    /**
     * Set imageProductName
     *
     * @param string $imageProductName
     * @return ProductImage
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
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return ProductImage
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
     * Set product
     *
     * @param \AppBundle\Entity\Product\Product $product
     * @return ProductImage
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
}