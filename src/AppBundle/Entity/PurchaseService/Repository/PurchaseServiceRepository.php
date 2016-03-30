<?php
// src/AppBundle/Entity/PurchaseService/Repository/PurchaseServiceRepository.php
namespace AppBundle\Entity\PurchaseService\Repository;

use AppBundle\Entity\Utility\Extended\ExtendedEntityRepository;

class PurchaseServiceRepository extends ExtendedEntityRepository
{
    public function findAllDesc()
    {
        $query = $this->createQueryBuilder('ps')
            ->select('ps, nt, st')
            ->leftJoin('ps.student', 'st')
            ->leftJoin('ps.nfcTag', 'nt')
            ->orderBy('ps.purchasedAt', 'DESC')
            ->getQuery()
        ;

        return $query->getResult();
    }

    public function find($id)
    {
        $query = $this->createQueryBuilder('ps')
            ->select('ps, nt, st')
            ->leftJoin('ps.student', 'st')
            ->leftJoin('ps.nfcTag', 'nt')
            ->where('ps.id = :id')
            ->setParameter(':id', $id)
            ->getQuery()
        ;

        return $query->getSingleResult();
    }
}
