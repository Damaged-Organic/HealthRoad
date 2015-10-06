<?php
// AppBundle/Entity/VendingMachine/VendingMachineSync.php
namespace AppBundle\Entity\VendingMachine;

use Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection;

use AppBundle\Entity\Utility\Traits\DoctrineMapping\IdMapperTrait;

/**
 * @ORM\Table(name="vending_machines_sync")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\VendingMachine\Repository\VendingMachineSyncRepository")
 */
class VendingMachineSync
{
    use IdMapperTrait;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\VendingMachine\VendingMachine", inversedBy="vendingMachineSyncs")
     * @ORM\JoinColumn(name="vending_machine_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $vendingMachine;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    protected $vendingMachineSyncId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $syncedType;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $syncedAt;

    /**
     * @ORM\Column(type="string", length=64)
     */
    protected $checksum;

    /**
     * @ORM\Column(type="text")
     */
    protected $data;

    /**
     * Set vendingMachineSyncId
     *
     * @param string $vendingMachineSyncId
     * @return VendingMachineSync
     */
    public function setVendingMachineSyncId($vendingMachineSyncId)
    {
        $this->vendingMachineSyncId = $vendingMachineSyncId;

        return $this;
    }

    /**
     * Get vendingMachineSyncId
     *
     * @return string 
     */
    public function getVendingMachineSyncId()
    {
        return $this->vendingMachineSyncId;
    }

    /**
     * Set syncedType
     *
     * @param string $syncedType
     * @return VendingMachineSync
     */
    public function setSyncedType($syncedType)
    {
        $this->syncedType = $syncedType;

        return $this;
    }

    /**
     * Get syncedType
     *
     * @return string
     */
    public function getSyncedType()
    {
        return $this->syncedType;
    }

    /**
     * Set syncedAt
     *
     * @param \DateTime $syncedAt
     * @return VendingMachineSync
     */
    public function setSyncedAt($syncedAt)
    {
        $this->syncedAt = $syncedAt;

        return $this;
    }

    /**
     * Get syncedAt
     *
     * @return \DateTime 
     */
    public function getSyncedAt()
    {
        return $this->syncedAt;
    }

    /**
     * Set checksum
     *
     * @param string $checksum
     * @return VendingMachineSync
     */
    public function setChecksum($checksum)
    {
        $this->checksum = $checksum;

        return $this;
    }

    /**
     * Get checksum
     *
     * @return string 
     */
    public function getChecksum()
    {
        return $this->checksum;
    }

    /**
     * Set data
     *
     * @param string $data
     * @return VendingMachineSync
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return string 
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set vendingMachine
     *
     * @param \AppBundle\Entity\VendingMachine\VendingMachine $vendingMachine
     * @return VendingMachineSync
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
}