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
            ->select('ta')
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
            'ta.transactionId',
        ];

        $this->baseChainSearchBy($searchBy, $entityFields);

        return $this;
    }
    // END: Extended find methods
}
