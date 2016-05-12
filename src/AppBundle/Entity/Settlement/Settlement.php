<?php
// AppBundle/Entity/Settlement/Settlement.php
namespace AppBundle\Entity\Settlement;

use Symfony\Component\Validator\Constraints as Assert,
    Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection;

use AppBundle\Entity\Utility\Traits\DoctrineMapping\IdMapperTrait;

/**
 * @ORM\Table(name="settlements")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Settlement\Repository\SettlementRepository")
 *
 * @UniqueEntity(fields="name", message="settlement.name.unique")
 */
class Settlement
{
    use IdMapperTrait;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Region\Region", inversedBy="settlements")
     * @ORM\JoinColumn(name="region_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $region;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\School\School", mappedBy="settlement")
     */
    protected $schools;

    /**
     * @ORM\Column(type="string", length=250, unique=true)
     *
     * @Assert\NotBlank(message="settlement.name.not_blank")
     * @Assert\Length(
     *      min=2,
     *      max=250,
     *      minMessage="settlement.name.length.min",
     *      maxMessage="settlement.name.length.max"
     * )
     */
    protected $name;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->schools = new ArrayCollection;
    }

    public function getSearchProperties()
    {
        $searchProperties = [
            $this->getName(),
        ];

        if( $this->getRegion() ) {
            $searchProperties[] = $this->getRegion()->getName();
        }

        return $searchProperties;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Settlement
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
     * Set region
     *
     * @param \AppBundle\Entity\Region\Region $region
     * @return Settlement
     */
    public function setRegion(\AppBundle\Entity\Region\Region $region = null)
    {
        $this->region = $region;

        return $this;
    }

    /**
     * Get region
     *
     * @return \AppBundle\Entity\Region\Region
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * Add school
     *
     * @param \AppBundle\Entity\School\School $school
     * @return Settlement
     */
    public function addSchool(\AppBundle\Entity\School\School $school)
    {
        $school->setSettlement($this);
        $this->schools[] = $school;

        return $this;
    }

    /**
     * Remove schools
     *
     * @param \AppBundle\Entity\School\School $schools
     */
    public function removeSchool(\AppBundle\Entity\School\School $schools)
    {
        $this->schools->removeElement($schools);
    }

    /**
     * Get schools
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSchools()
    {
        return $this->schools;
    }
}
