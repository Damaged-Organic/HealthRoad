<?php
// AppBundle/Entity/Purchase/Repository/PurchaseRepository.php
namespace AppBundle\Entity\Purchase\Repository;

use AppBundle\Entity\Utility\Extended\ExtendedEntityRepository;

class PurchaseRepository extends ExtendedEntityRepository
{
    public function findSumsByStudentsWithSyncId($syncId)
    {
        $query = $this->createQueryBuilder('p')
            ->select('nt.code, SUM(pr.price) as price_sum')
            ->leftJoin('p.nfcTag', 'nt')
            ->leftJoin('p.product', 'pr')
            ->where('p.vendingMachineSyncId = :syncId')
            ->setParameter('syncId', $syncId)
            ->groupBy('nt.code')
            ->getQuery()
        ;

        return $query->getResult();
    }
}