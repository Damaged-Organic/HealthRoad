<?php
// AppBundle/Entity/Utility/Traits/DoctrineMapping/PseudoDeleteMapperTrait.php
namespace AppBundle\Entity\Utility\Traits\DoctrineMapping;

use DateTime;

use Doctrine\ORM\Mapping as ORM;

trait PseudoDeleteMapperTrait
{
    /**
     * @ORM\Column(type="boolean")
     */
    protected $pseudoDeleted = FALSE;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $pseudoDeleteAt;

    /**
     * Set pseudoDeleted
     *
     * @param boolean $pseudoDeleted
     * @return $this
     */
    public function setPseudoDeleted($pseudoDeleted)
    {
        $this->pseudoDeleted = $pseudoDeleted;

        $this->setDeleteAt(
            ( $pseudoDeleted ) ? (new DateTime)->modify('+1 month') : NULL
        );

        return $this;
    }

    /**
     * Get pseudoDeleted
     *
     * @return boolean
     */
    public function getPseudoDeleted()
    {
        return $this->pseudoDeleted;
    }

    /**
     * Set pseudoDeleteAt
     *
     * @param \DateTime $pseudoDeleteAt
     * @return $this
     */
    public function setDeleteAt($pseudoDeleteAt)
    {
        $this->pseudoDeleteAt = $pseudoDeleteAt;

        return $this;
    }

    /**
     * Get pseudoDeleteAt
     *
     * @return \DateTime
     */
    public function getDeleteAt()
    {
        return $this->pseudoDeleteAt;
    }
}