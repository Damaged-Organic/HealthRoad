<?php
// AppBundle/Entity/Website/Menu/Menu.php
namespace AppBundle\Entity\Website\Menu;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Doctrine\ORM\Mapping as ORM;

use AppBundle\Entity\Utility\Traits\DoctrineMapping\IdMapperTrait;

/**
 * @ORM\Table(name="website_menu")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Website\Menu\Repository\MenuRepository")
 *
 * @UniqueEntity(fields="alias")
 */
class Menu
{
    use IdMapperTrait;

    /**
     * @ORM\Column(type="string", length=200, unique=true)
     */
    protected $route;

    /**
     * @ORM\Column(type="string", length=200)
     */
    protected $block;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $titleShort;

    /**
     * @ORM\Column(type="string", length=250, nullable=true)
     */
    protected $titleFull;

    /**
     * To string
     */
    public function __toString()
    {
        return ( $this->getTitleShort() ) ?: '';
    }

    /**
     * Set route
     *
     * @param string $route
     * @return Menu
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
     * Set block
     *
     * @param string $block
     * @return Menu
     */
    public function setBlock($block)
    {
        $this->block = $block;

        return $this;
    }

    /**
     * Get block
     *
     * @return string
     */
    public function getBlock()
    {
        return $this->block;
    }

    /**
     * Set titleShort
     *
     * @param string $titleShort
     * @return Menu
     */
    public function setTitleShort($titleShort)
    {
        $this->titleShort = $titleShort;

        return $this;
    }

    /**
     * Get titleShort
     *
     * @return string 
     */
    public function getTitleShort()
    {
        return $this->titleShort;
    }

    /**
     * Set titleFull
     *
     * @param string $titleFull
     * @return Menu
     */
    public function setTitleFull($titleFull)
    {
        $this->titleFull = $titleFull;

        return $this;
    }

    /**
     * Get titleFull
     *
     * @return string 
     */
    public function getTitleFull()
    {
        return $this->titleFull;
    }
}