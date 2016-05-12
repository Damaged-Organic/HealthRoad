<?php
// AppBundle/Entity/VendingMachine/VendingMachineLoad.php
namespace AppBundle\Entity\VendingMachine;

use Doctrine\ORM\Mapping as ORM;

use AppBundle\Entity\Utility\Traits\DoctrineMapping\IdMapperTrait,
    AppBundle\Entity\VendingMachine\Utility\Interfaces\SyncVendingMachineLoadPropertiesInterface;

/**
 * @ORM\Table(name="vending_machines_load")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\VendingMachine\Repository\VendingMachineLoadRepository")
 */
class VendingMachineLoad implements SyncVendingMachineLoadPropertiesInterface
{
    use IdMapperTrait;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\VendingMachine\VendingMachine", inversedBy="vendingMachineLoad")
     * @ORM\JoinColumn(name="vending_machine_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $vendingMachine;

    /**
     * @ORM\Column(type="bigint")
     */
    protected $productId;

    /**
     * @ORM\Column(type="integer")
     */
    protected $productQuantity;

    /**
     * @ORM\Column(type="integer")
     */
    protected $springPosition;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $loadedAt;

    public function getSearchProperties()
    {
        $searchProperties = [];

        if( $this->getVendingMachine() ) {
            $searchProperties[] = $this->getVendingMachine()->getSerial();
        }

        if( $this->getLoadedAt() ) {
            $searchProperties[] = $this->getLoadedAt()->format('Y-m-d H:i:s');
        }

        return $searchProperties;
    }

    /**
     * Set productId
     *
     * @param integer $productId
     * @return VendingMachineLoad
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;

        return $this;
    }

    /**
     * Get productId
     *
     * @return integer
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * Set productQuantity
     *
     * @param integer $productQuantity
     * @return VendingMachineLoad
     */
    public function setProductQuantity($productQuantity)
    {
        $this->productQuantity = $productQuantity;

        return $this;
    }

    /**
     * Get productQuantity
     *
     * @return integer
     */
    public function getProductQuantity()
    {
        return $this->productQuantity;
    }

    /**
     * Set springPosition
     *
     * @param integer $springPosition
     * @return VendingMachineLoad
     */
    public function setSpringPosition($springPosition)
    {
        $this->springPosition = $springPosition;

        return $this;
    }

    /**
     * Get springPosition
     *
     * @return integer
     */
    public function getSpringPosition()
    {
        return $this->springPosition;
    }

    /**
     * Set vendingMachine
     *
     * @param \AppBundle\Entity\VendingMachine\VendingMachine $vendingMachine
     * @return VendingMachineLoad
     */
    public function setVendingMachine(\AppBundle\Entity\VendingMachine\VendingMachine $vendingMachine = null)
    {
        $this->vendingMachine = $vendingMachine;

        return $this;
    }

    /**
     * Get vendingMachine
     *
     * @return \AppBundle\Entity\VendingMachine\VendingMachine
     */
    public function getVendingMachine()
    {
        return $this->vendingMachine;
    }

    /**
     * Set occurredAt
     *
     * @param \DateTime $loadedAt
     * @return VendingMachineLoad
     */
    public function setLoadedAt($loadedAt)
    {
        $this->loadedAt = $loadedAt;

        return $this;
    }

    /**
     * Get loadedAt
     *
     * @return \DateTime
     */
    public function getLoadedAt()
    {
        return $this->loadedAt;
    }

    static public function getSyncArrayName()
    {
        return self::VENDING_MACHINE_LOAD_ARRAY;
    }
}
