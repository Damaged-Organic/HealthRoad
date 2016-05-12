<?php
// AppBundle/Entity/School/Repository/SchoolRepository.php
namespace AppBundle\Entity\School\Repository;

use AppBundle\Entity\Utility\Extended\ExtendedEntityRepository;

class SchoolRepository extends ExtendedEntityRepository
{
    // BEGIN: Extended find methods
    public function findChained()
    {
        $this->chain = $this->createQueryBuilder('s')
            ->select('s, sm, r')
            ->leftJoin('s.settlement', 'sm')
            ->leftJoin('sm.region', 'r')
        ;

        return $this;
    }

    public function chainFindBy(array $findBy)
    {
        $this->baseChainFindBy($findBy, 's');

        return $this;
    }

    public function chainSearchBy($searchBy)
    {
        $entityFields = [
            's.name', 's.address',
            'sm.name',
            'r.name',
        ];

        $this->baseChainSearchBy($searchBy, $entityFields);

        return $this;
    }
    // END: Extended find methods
}
