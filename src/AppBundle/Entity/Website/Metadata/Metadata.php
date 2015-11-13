<?php
// AppBundle/Entity/Website/Metadata/Metadata.php
namespace AppBundle\Entity\Website\Metadata;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Doctrine\ORM\Mapping as ORM;

use AppBundle\Entity\Utility\Traits\DoctrineMapping\IdMapperTrait;

/**
 * @ORM\Table(name="website_metadata")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Website\Metadata\Repository\MetadataRepository")
 *
 * @UniqueEntity(fields="route")
 */
class Metadata
{
    use IdMapperTrait;

    /**
     * @ORM\Column(type="string", length=100, unique=true)
     */
    protected $route;

    /**
     * @ORM\Column(type="string", length=250)
     */
    protected $title;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $robots;

    /**
     * To string
     */
    public function __toString()
    {
        return ( $this->getTitle() ) ?: '';
    }

    /**
     * Set route
     *
     * @param string $route
     * @return Metadata
     */
    public function setRoute($route)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * Get route
     *
     * @return string 
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Metadata
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Metadata
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
     * Set robots
     *
     * @param string $robots
     * @return Metadata
     */
    public function setRobots($robots)
    {
        $this->robots = $robots;

        return $this;
    }

    /**
     * Get robots
     *
     * @return string 
     */
    public function getRobots()
    {
        return $this->robots;
    }
}
