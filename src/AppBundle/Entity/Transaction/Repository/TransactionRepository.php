<?php
// src/AppBundle/Entity/Transaction/Repository/TransactionRepository.php
namespace AppBundle\Entity\Transaction\Repository;

use AppBundle\Entity\Utility\Extended\ExtendedEntityRepository;

class TransactionRepository extends ExtendedEntityRepository
{
    // BEGIN: Extended find methods
    public function findChained()
    {
        $this->chain = $this->createQueryBuilder('ta')
            ->select('ta, nt, st, s, vm')
            ->leftJoin('ta.vendingMachine', 'vm')
            ->leftJoin('ta.nfcTag', 'nt')
            ->leftJoin('ta.student', 'st')
            ->leftJoin('st.school', 's')
            ->orderBy('ta.id', 'DESC')
        ;

        return $this;
    }

    public function chainFindBy(array $findBy)
    {
        $this->baseChainFindBy($findBy, 'ta');

        return $this;
    }

    public function chainSearchBy($searchBy)
    {
        $entityFields = [
            'ta.syncTransactionId',
            'vm.serial',
            'nt.number',
            'st.name', 'st.surname', 'st.patronymic',
        ];

        $this->baseChainSearchBy($searchBy, $entityFields);

        return $this;
    }
    // END: Extended find methods
}
