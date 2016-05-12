<?php
// AppBundle/Entity/Region/Repository/RegionRepository.php
namespace AppBundle\Entity\Region\Repository;

use AppBundle\Entity\Utility\Extended\ExtendedEntityRepository;

class RegionRepository extends ExtendedEntityRepository
{
    // BEGIN: Extended find methods
    public function findChained()
    {
        $this->chain = $this->createQueryBuilder('r')
            ->select('r')
        ;

        return $this;
    }

    public function chainFindBy(array $findBy)
    {
        $this->baseChainFindBy($findBy, 'r');

        return $this;
    }

    public function chainSearchBy($searchBy)
    {
        $entityFields = [
            'r.name', 'r.administrativeCenter', 'r.phoneCode',
        ];

        $this->baseChainSearchBy($searchBy, $entityFields);

        return $this;
    }
    // END: Extended find methods
}
