<?php
// AppBundle/Entity/Utility/Traits/DoctrineMapping/SlugMapper.php
namespace AppBundle\Entity\Utility\Traits\DoctrineMapping;

use Doctrine\ORM\Mapping as ORM;

use Gedmo\Mapping\Annotation as Gedmo;

trait SlugMapper
{
    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }
}