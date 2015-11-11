<?php
// AppBundle/Entity/WebsiteMenu/WebsiteMenu.php
namespace AppBundle\Entity\WebsiteMenu;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Doctrine\ORM\Mapping as ORM;

use AppBundle\Entity\Utility\Traits\DoctrineMapping\IdMapperTrait;

/**
 * @ORM\Table(name="website_menu")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\WebsiteMenu\Repository\WebsiteMenuRepository")
 *
 * @UniqueEntity(fields="alias")
 */
class WebsiteMenu
{
    use IdMapperTrait;

    /**
     * @ORM\Column(type="string", length=200)
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
        return $this->getTitleShort();
    }

    /**
     * Set route
     *
     * @param string $route
     * @return WebsiteMenu
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
     * @return WebsiteMenu
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
     * @return WebsiteMenu
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
     * @return WebsiteMenu
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