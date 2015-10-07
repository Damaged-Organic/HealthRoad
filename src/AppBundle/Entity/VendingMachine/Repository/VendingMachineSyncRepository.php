<?php
// AppBundle/Entity/VendingMachine/Repository/VendingMachineSyncRepository.php
namespace AppBundle\Entity\VendingMachine\Repository;

use AppBundle\Entity\Utility\Extended\ExtendedEntityRepository;

class VendingMachineSyncRepository extends ExtendedEntityRepository
{
    public function findLatestByVendingMachineSyncId($syncedType)
    {
        $query = $this->createQueryBuilder('vms')
            ->select('vms')
            ->where('vms.syncedType = :syncedType')
            ->setParameter('syncedType', $syncedType)
            ->orderBy('vms.syncedAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
        ;

        return $query->getOneOrNullResult();
    }
}