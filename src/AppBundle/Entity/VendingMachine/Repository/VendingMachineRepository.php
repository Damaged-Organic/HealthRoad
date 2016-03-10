<?php
// AppBundle/Entity/VendingMachine/Repository/VendingMachineRepository.php
namespace AppBundle\Entity\VendingMachine\Repository;

use AppBundle\Entity\Utility\Extended\ExtendedEntityRepository;

class VendingMachineRepository extends ExtendedEntityRepository
{
    public function findOneBySerialPrefetchRelated($serial)
    {
        $query = $this->createQueryBuilder('vm')
            ->select('vm, s, st, nt, p')
            ->leftJoin('vm.school', 's')
            ->leftJoin('s.students', 'st')
            ->leftJoin('st.nfcTag', 'nt')
            ->leftJoin('st.products', 'p')
            ->where('vm.serial = :serial')
            ->setParameter(':serial', $serial)
            ->getQuery()
        ;

        return $query->getSingleResult();
    }

    public function findReadyByPurchaseSum($sum)
    {
        $query = $this->createQueryBuilder('vm')
            ->select('vm, p, pr, SUM(pr.price) AS purchaseSum')
            ->leftJoin('vm.purchases', 'p')
            ->leftJoin('p.product', 'pr')
            ->where('vm.vendingMachineLoadedAt IS NOT NULL')
            ->groupBy('vm')
            ->getQuery()
        ;

        return $query->getResult();
    }

    public function findReadyByLoadDate()
    {
        $query = $this->_em->createQueryBuilder()
            ->from('AppBundle:VendingMachine\VendingMachine', 'vm', 'vm.id')
            ->select('vm, s, se, r, p, pr')
            ->leftJoin('vm.school', 's')
            ->leftJoin('s.settlement', 'se')
            ->leftJoin('se.region', 'r')
            ->leftJoin('vm.purchases', 'p')
            ->leftJoin('p.product', 'pr')
            ->where('vm.vendingMachineLoadedAt IS NOT NULL')
            ->andWhere('p.syncPurchasedAt >= vm.vendingMachineLoadedAt')
            ->getQuery()
        ;

        return $query->getResult();
    }
}
