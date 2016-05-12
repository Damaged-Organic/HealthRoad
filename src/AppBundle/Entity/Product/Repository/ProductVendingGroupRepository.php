<?php
// AppBundle/Entity/Product/Repository/ProductVendingGroupRepository.php
namespace AppBundle\Entity\Product\Repository;

use AppBundle\Entity\Utility\Extended\ExtendedEntityRepository;

class ProductVendingGroupRepository extends ExtendedEntityRepository
{
    // BEGIN: Extended find methods
    public function findChained()
    {
        $this->chain = $this->createQueryBuilder('vmg')
            ->select('vmg')
        ;

        return $this;
    }

    public function chainFindBy(array $findBy)
    {
        $this->baseChainFindBy($findBy, 'vmg');

        return $this;
    }

    public function chainSearchBy($searchBy)
    {
        $entityFields = [
            'vmg.name',
        ];

        $this->baseChainSearchBy($searchBy, $entityFields);

        return $this;
    }
    // END: Extended find methods
}
