<?php
// AppBundle/Entity/TestEntity.php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use AppBundle\Entity\Utility\Traits\DoctrineMapping\IdMapperTrait;

/**
 * @ORM\Entity()
 * @ORM\Table(name="test_entities")
 */
class TestEntity
{
    use IdMapperTrait;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $syncDate;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $syncJson;

    public function setSyncDate($syncDate)
    {
        $this->syncDate = $syncDate;

        return $this;
    }

    public function getSyncDate()
    {
        return $this->syncDate;
    }

    public function setSyncJson($syncJson)
    {
        $this->syncJson = $syncJson;

        return $this;
    }

    public function getSyncJson()
    {
        return $this->syncJson;
    }
}
