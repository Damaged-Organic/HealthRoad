<?php
// AppBundle/Entity/Region/Region.php
namespace AppBundle\Entity\Region;

use Symfony\Component\Validator\Constraints as Assert,
    Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection;

use AppBundle\Entity\Utility\Traits\DoctrineMapping\IdMapperTrait;

/**
 * @ORM\Table(name="regions")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Region\Repository\RegionRepository")
 *
 * @UniqueEntity(fields="name", message="region.name.unique")
 */
class Region
{
    use IdMapperTrait;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Employee\Employee", inversedBy="regions")
     * @ORM\JoinColumn(name="employee_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $employee;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Settlement\Settlement", mappedBy="region")
     */
    protected $settlements;

    /**
     * @ORM\Column(type="string", length=250, unique=true)
     *
     * @Assert\NotBlank(message="region.name.not_blank")
     * @Assert\Length(
     *      min=2,
     *      max=250,
     *      minMessage="region.name.length.min",
     *      maxMessage="region.name.length.max"
     * )
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=250, nullable=true)
     *
     * @Assert\Length(
     *      min=2,
     *      max=250,
     *      minMessage="region.administrative_center.length.min",
     *      maxMessage="region.administrative_center.length.max"
     * )
     */
    protected $administrativeCenter;

    /**
     * @ORM\Column(type="string", length=2, nullable=true)
     *
     * @Assert\Regex(
     *     pattern="/^[0-9]{2}$/",
     *     message="region.phone_code.regex"
     * )
     */
    protected $phoneCode;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->settlements = new ArrayCollection;
    }

    public function getSearchProperties()
    {
        return [
            $this->getName(),
            $this->getAdministrativeCenter(),
            $this->getPhoneCode(),
        ];
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Region
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
     * Set employee
     *
     * @param \AppBundle\Entity\Employee\Employee $employee
     * @return Region
     */
    public function setEmployee(\AppBundle\Entity\Employee\Employee $employee = null)
    {
        $this->employee = $employee;

        return $this;
    }

    /**
     * Get employee
     *
     * @return \AppBundle\Entity\Employee\Employee
     */
    public function getEmployee()
    {
        return $this->employee;
    }

    /**
     * Add settlements
     *
     * @param \AppBundle\Entity\Settlement\Settlement $settlements
     * @return Region
     */
    public function addSettlement(\AppBundle\Entity\Settlement\Settlement $settlement)
    {
        $settlement->setRegion($this);
        $this->settlements[] = $settlement;

        return $this;
    }

    /**
     * Remove settlements
     *
     * @param \AppBundle\Entity\Settlement\Settlement $settlements
     */
    public function removeSettlement(\AppBundle\Entity\Settlement\Settlement $settlements)
    {
        $this->settlements->removeElement($settlements);
    }

    /**
     * Get settlements
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSettlements()
    {
        return $this->settlements;
    }

    /**
     * Set administrativeCenter
     *
     * @param string $administrativeCenter
     * @return Region
     */
    public function setAdministrativeCenter($administrativeCenter)
    {
        $this->administrativeCenter = $administrativeCenter;

        return $this;
    }

    /**
     * Get administrativeCenter
     *
     * @return string
     */
    public function getAdministrativeCenter()
    {
        return $this->administrativeCenter;
    }

    /**
     * Set phoneCode
     *
     * @param integer $phoneCode
     * @return Region
     */
    public function setPhoneCode($phoneCode)
    {
        $this->phoneCode = $phoneCode;

        return $this;
    }

    /**
     * Get phoneCode
     *
     * @return integer
     */
    public function getPhoneCode()
    {
        return $this->phoneCode;
    }
}
