<?php
// AppBundle/Entity/Setting/Repository/SettingRepository.php
namespace AppBundle\Entity\Setting\Repository;

use AppBundle\Entity\Utility\Extended\ExtendedEntityRepository;

class SettingRepository extends ExtendedEntityRepository
{
    public function findOne()
    {
        $query = $this
            ->createQueryBuilder('s')
            ->select('s')
            ->setMaxResults(1)
            ->orderBy('s.id', 'ASC')
            ->getQuery()
        ;

        return $query->getSingleResult();
    }
}