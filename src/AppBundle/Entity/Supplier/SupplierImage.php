<?php
// AppBundle/Entity/Supplier/SupplierImage.php
namespace AppBundle\Entity\Supplier;

use DateTime;

use Symfony\Component\Validator\Constraints as Assert,
    Symfony\Component\HttpFoundation\File\File;

use Doctrine\ORM\Mapping as ORM;

use Vich\UploaderBundle\Mapping\Annotation as Vich;

use AppBundle\Entity\Utility\Traits\DoctrineMapping\IdMapperTrait;

/**
 * @ORM\Table(name="suppliers_images")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Supplier\Repository\SupplierImageRepository")
 *
 * @Vich\Uploadable
 */
class SupplierImage
{
    use IdMapperTrait;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Supplier\Supplier", inversedBy="supplierImages")
     * @ORM\JoinColumn(name="supplier_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    protected $supplier;

    /**
     * @Assert\File(
     *     maxSize="2M",
     *     mimeTypes={"image/png", "image/jpeg", "image/pjpeg"}
     * )
     *
     * @Vich\UploadableField(mapping="supplier_image", fileNameProperty="imageSupplierName")
     */
    protected $imageSupplierFile;

    /**
     * @ORM\Column(type="string", length=250, nullable=true)
     */
    protected $imageSupplierName;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $updatedAt;

    /* Vich Uploadable Methods */

    public function setImageSupplierFile(File $imageSupplier = NULL)
    {
        $this->imageSupplierFile = $imageSupplier;

        if( $imageSupplier )
            $this->updatedAt = new DateTime('now');

        return $this;
    }

    public function getImageSupplierFile()
    {
        return $this->imageSupplierFile;
    }

    /* End \ Vich Uploadable Methods */

    /**
     * Set imageSupplierName
     *
     * @param string $imageSupplierName
     * @return SupplierImage
     */
    public function setImageSupplierName($imageSupplierName)
    {
        $this->imageSupplierName = $imageSupplierName;

        return $this;
    }

    /**
     * Get imageSupplierName
     *
     * @return string 
     */
    public function getImageSupplierName()
    {
        return $this->imageSupplierName;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return SupplierImage
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
     * Set supplier
     *
     * @param \AppBundle\Entity\Supplier\Supplier $supplier
     * @return SupplierImage
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
}