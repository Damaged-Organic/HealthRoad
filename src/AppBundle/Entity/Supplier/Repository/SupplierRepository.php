<?php
// AppBundle/Entity/Supplier/Repository/SupplierRepository.php
namespace AppBundle\Entity\Supplier\Repository;

use AppBundle\Entity\Utility\Extended\ExtendedEntityRepository;

class SupplierRepository extends ExtendedEntityRepository
{
    // BEGIN: Extended find methods
    public function findChained()
    {
        $this->chain = $this->createQueryBuilder('sp')
            ->select('sp')
        ;

        return $this;
    }

    public function chainFindBy(array $findBy)
    {
        $this->baseChainFindBy($findBy, 'sp');

        return $this;
    }

    public function chainSearchBy($searchBy)
    {
        $entityFields = [
            'sp.name', 'sp.phoneNumberSupplier', 'sp.emailSupplier',
        ];

        $this->baseChainSearchBy($searchBy, $entityFields);

        return $this;
    }
    // END: Extended find methods
}
