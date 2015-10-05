<?php
// AppBundle/Entity/Utility/Traits/DoctrineMapping/IdMapperTrait.php
namespace AppBundle\Entity\Utility\Traits\DoctrineMapping;

use Doctrine\ORM\Mapping as ORM;

trait IdMapperTrait
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    protected $id;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
}