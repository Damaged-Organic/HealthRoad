<?php
// AppBundle/Entity/Product/ProductVendingGroup.php
namespace AppBundle\Entity\Product;

use Symfony\Component\Validator\Constraints as Assert,
    Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection;

use AppBundle\Entity\Utility\Traits\DoctrineMapping\IdMapperTrait,
    AppBundle\Entity\Utility\Traits\DoctrineMapping\PseudoDeleteMapperTrait;

/**
 * @ORM\Table(name="products_vending_groups")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Product\Repository\ProductVendingGroupRepository")
 *
 * @UniqueEntity(fields="name", message="product_vending_group.name.unique")
 */
class ProductVendingGroup
{
    use IdMapperTrait, PseudoDeleteMapperTrait;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\VendingMachine\VendingMachine", mappedBy="productVendingGroup")
     */
    protected $vendingMachines;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Product\Product", inversedBy="productVendingGroups", indexBy="id")
     * @ORM\JoinTable(name="products_product_vending_groups")
     * @ORM\OrderBy({"displayOrder" = "ASC"})
     */
    protected $products;

    /**
     * @ORM\Column(type="string", length=250, unique=true)
     *
     * @Assert\NotBlank(message="product_vending_group.name.not_blank")
     *
     * @Assert\Length(
     *      min=2,
     *      max=250,
     *      minMessage="product_vending_group.name.length.min",
     *      maxMessage="product_vending_group.name.length.max"
     * )
     */
    protected $name;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->vendingMachines = new ArrayCollection;
        $this->products        = new ArrayCollection;
    }

    public function getSearchProperties()
    {
        return [
            $this->getName(),
        ];
    }

    /**
     * Set name
     *
     * @param string $name
     * @return ProductVendingGroup
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
     * Add vendingMachines
     *
     * @param \AppBundle\Entity\VendingMachine\VendingMachine $vendingMachine
     * @return ProductVendingGroup
     */
    public function addVendingMachine(\AppBundle\Entity\VendingMachine\VendingMachine $vendingMachine)
    {
        $vendingMachine->setProductVendingGroup($this);
        $this->vendingMachines[] = $vendingMachine;

        return $this;
    }

    /**
     * Remove vendingMachines
     *
     * @param \AppBundle\Entity\VendingMachine\VendingMachine $vendingMachines
     */
    public function removeVendingMachine(\AppBundle\Entity\VendingMachine\VendingMachine $vendingMachines)
    {
        $this->vendingMachines->removeElement($vendingMachines);
    }

    /**
     * Get vendingMachines
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVendingMachines()
    {
        return $this->vendingMachines;
    }

    /**
     * Add products
     *
     * @param \AppBundle\Entity\Product\Product $product
     * @return ProductVendingGroup
     */
    public function addProduct(\AppBundle\Entity\Product\Product $product)
    {
        $product->addProductVendingGroup($this);
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
