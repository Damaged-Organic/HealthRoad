<?php
// AppBundle/Entity/Utility/Extended/ExtendedEntityRepository.php
namespace AppBundle\Entity\Utility\Extended;

use Doctrine\ORM\EntityRepository;

class ExtendedEntityRepository extends EntityRepository
{
    public function count()
    {
        $query = $this
            ->createQueryBuilder('e')
            ->select('count(e.id)')
            ->getQuery();

        return $query->getSingleScalarResult();
    }
}