<?php
// AppBundle/Entity/Product/ProductCategory.php
namespace AppBundle\Entity\Product;

use Symfony\Component\Validator\Constraints as Assert,
    Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection;

use AppBundle\Entity\Utility\Traits\DoctrineMapping\IdMapperTrait;

/**
 * @ORM\Table(name="products_categories")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Product\Repository\ProductCategoryRepository")
 *
 * @UniqueEntity(fields="name", message="product_category.name.unique")
 */
class ProductCategory
{
    use IdMapperTrait;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Product\Product", mappedBy="productCategory")
     */
    protected $products;

    /**
     * @ORM\Column(type="string", length=250, unique=true)
     *
     * @Assert\NotBlank(message="product_category.name.not_blank")
     * @Assert\Length(
     *      min=2,
     *      max=250,
     *      minMessage="product_category.name.length.min",
     *      maxMessage="product_category.name.length.max"
     * )
     */
    protected $name;

    /** Кондитерские изделия, Соки, Вода, Фрукты */

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->products = new ArrayCollection;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return ProductCategory
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
     * Add products
     *
     * @param \AppBundle\Entity\Product\Product $product
     * @return ProductCategory
     */
    public function addProduct(\AppBundle\Entity\Product\Product $product)
    {
        $product->setProductCategory($this);
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