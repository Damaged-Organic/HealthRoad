<?php
// AppBundle/Entity/VendingMachine/VendingMachineEvent.php
namespace AppBundle\Entity\VendingMachine;

use Doctrine\ORM\Mapping as ORM;

use AppBundle\Entity\Utility\Traits\DoctrineMapping\IdMapperTrait,
    AppBundle\Entity\VendingMachine\Utility\Interfaces\SyncVendingMachineEventPropertiesInterface;

/**
 * @ORM\Table(name="vending_machines_events")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\VendingMachine\Repository\VendingMachineEventRepository")
 */
class VendingMachineEvent implements SyncVendingMachineEventPropertiesInterface
{
    use IdMapperTrait;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\VendingMachine\VendingMachine", inversedBy="vendingMachineEvents")
     * @ORM\JoinColumn(name="vending_machine_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $vendingMachine;

    /**
     * @ORM\Column(type="integer")
     */
    protected $syncEventId;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $occurredAt;

    /**
     * @ORM\Column(type="string", length=32)
     */
    protected $type;

    /**
     * @ORM\Column(type="smallint")
     */
    protected $code;

    /**
     * @ORM\Column(type="string", length=250, nullable=true)
     */
    protected $message;

    public function getSearchProperties()
    {
        $searchProperties = [];

        if( $this->getVendingMachine() ) {
            $searchProperties[] = $this->getVendingMachine()->getSerial();
        }

        if( $this->getOccurredAt() ) {
            $searchProperties[] = $this->getOccurredAt()->format('Y-m-d H:i:s');
        }

        return $searchProperties;
    }

    /**
     * Set syncEventId
     *
     * @param integer $syncEventId
     * @return VendingMachineEvent
     */
    public function setSyncEventId($syncEventId)
    {
        $this->syncEventId = $syncEventId;

        return $this;
    }

    /**
     * Get syncEventId
     *
     * @return integer
     */
    public function getSyncEventId()
    {
        return $this->syncEventId;
    }

    /**
     * Set occurredAt
     *
     * @param \DateTime $occurredAt
     * @return VendingMachineEvent
     */
    public function setOccurredAt($occurredAt)
    {
        $this->occurredAt = $occurredAt;

        return $this;
    }

    /**
     * Get occurredAt
     *
     * @return \DateTime
     */
    public function getOccurredAt()
    {
        return $this->occurredAt;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return VendingMachineEvent
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set code
     *
     * @param integer $code
     * @return VendingMachineEvent
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return integer
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set message
     *
     * @param string $message
     * @return VendingMachineEvent
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set vendingMachine
     *
     * @param \AppBundle\Entity\VendingMachine\VendingMachine $vendingMachine
     * @return VendingMachineEvent
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

    static public function getSyncArrayName()
    {
        return self::VENDING_MACHINE_EVENT_ARRAY;
    }
}
