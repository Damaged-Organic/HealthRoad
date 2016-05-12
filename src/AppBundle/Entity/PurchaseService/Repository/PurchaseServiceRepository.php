<?php
// src/AppBundle/Entity/PurchaseService/Repository/PurchaseServiceRepository.php
namespace AppBundle\Entity\PurchaseService\Repository;

use AppBundle\Entity\Utility\Extended\ExtendedEntityRepository;

class PurchaseServiceRepository extends ExtendedEntityRepository
{
    // BEGIN: Extended find methods
    public function findChained()
    {
        $this->chain = $this->createQueryBuilder('ps')
            ->select('ps, nt, st')
            ->leftJoin('ps.nfcTag', 'nt')
            ->leftJoin('ps.student', 'st')
            ->orderBy('ps.purchasedAt', 'DESC')
        ;

        return $this;
    }

    public function chainFindBy(array $findBy)
    {
        $this->baseChainFindBy($findBy, 'ps');

        return $this;
    }

    public function chainSearchBy($searchBy)
    {
        $entityFields = [
            'ps.item',
            'nt.number',
            'st.name', 'st.surname', 'st.patronymic',
        ];

        $this->baseChainSearchBy($searchBy, $entityFields);

        return $this;
    }
    // END: Extended find methods

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
