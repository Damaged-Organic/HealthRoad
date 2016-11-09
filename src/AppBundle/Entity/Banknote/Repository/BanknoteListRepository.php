<?php
// src/AppBundle/Entity/Banknote/Repository/BanknoteListRepository.php
namespace AppBundle\Entity\Banknote\Repository;

use AppBundle\Entity\Utility\Extended\ExtendedEntityRepository;

class BanknoteListRepository extends ExtendedEntityRepository
{
    // BEGIN: Extended find methods
    public function findChained()
    {
        $this->chain = $this->createQueryBuilder('bl')
            ->select('bl, b, ta')
            ->leftJoin('bl.banknote', 'b')
            ->leftJoin('bl.transaction', 'ta')
        ;

        return $this;
    }

    public function chainFindBy(array $findBy)
    {
        $this->baseChainFindBy($findBy, 'bl');

        return $this;
    }

    public function chainSearchBy($searchBy)
    {
        $entityFields = [
            'bl.id',
        ];

        $this->baseChainSearchBy($searchBy, $entityFields);

        return $this;
    }
    // END: Extended find methods
}
