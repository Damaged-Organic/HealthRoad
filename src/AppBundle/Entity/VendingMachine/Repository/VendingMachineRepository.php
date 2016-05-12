<?php
// AppBundle/Entity/VendingMachine/Repository/VendingMachineRepository.php
namespace AppBundle\Entity\VendingMachine\Repository;

use AppBundle\Entity\Utility\Extended\ExtendedEntityRepository;

class VendingMachineRepository extends ExtendedEntityRepository
{
    // BEGIN: Extended find methods
    public function findChained()
    {
        $this->chain = $this->createQueryBuilder('vm')
            ->select('vm, vmg, s, sm, r, st, nt, p')
            ->leftJoin('vm.productVendingGroup', 'vmg')
            ->leftJoin('vm.school', 's')
            ->leftJoin('s.settlement', 'sm')
            ->leftJoin('sm.region', 'r')
            ->leftJoin('s.students', 'st')
            ->leftJoin('st.nfcTag', 'nt')
            ->leftJoin('st.products', 'p')
        ;

        return $this;
    }

    public function chainFindBy(array $findBy)
    {
        $this->baseChainFindBy($findBy, 'vm');

        return $this;
    }

    public function chainSearchBy($searchBy)
    {
        $entityFields = [
            'vm.serial', 'vm.name',
            'vmg.name',
            's.name', 's.address',
            'sm.name',
            'r.name',
        ];

        $this->baseChainSearchBy($searchBy, $entityFields);

        return $this;
    }
    // END: Extended find methods

    public function findOneBySerialPrefetchRelated($serial)
    {
        $query = $this->createQueryBuilder('vm')
            ->select('vm, vmg, s, sm, r, st, nt, p')
            ->leftJoin('vm.productVendingGroup', 'vmg')
            ->leftJoin('vm.school', 's')
            ->leftJoin('s.settlement', 'sm')
            ->leftJoin('sm.region', 'r')
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
