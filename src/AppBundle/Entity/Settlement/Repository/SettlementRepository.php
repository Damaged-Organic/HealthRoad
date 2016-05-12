<?php
// AppBundle/Entity/Settlement/Repository/SettlementRepository.php
namespace AppBundle\Entity\Settlement\Repository;

use AppBundle\Entity\Utility\Extended\ExtendedEntityRepository;

class SettlementRepository extends ExtendedEntityRepository
{
    // BEGIN: Extended find methods
    public function findChained()
    {
        $this->chain = $this->createQueryBuilder('sm')
            ->select('sm, r')
            ->leftJoin('sm.region', 'r')
        ;

        return $this;
    }

    public function chainFindBy(array $findBy)
    {
        $this->baseChainFindBy($findBy, 'sm');

        return $this;
    }

    public function chainSearchBy($searchBy)
    {
        $entityFields = [
            'sm.name',
            'r.name',
        ];

        $this->baseChainSearchBy($searchBy, $entityFields);

        return $this;
    }
    // END: Extended find methods
}
