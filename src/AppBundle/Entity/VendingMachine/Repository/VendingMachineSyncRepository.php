<?php
// AppBundle/Entity/VendingMachine/Repository/VendingMachineSyncRepository.php
namespace AppBundle\Entity\VendingMachine\Repository;

use AppBundle\Entity\Utility\Extended\ExtendedEntityRepository;

class VendingMachineSyncRepository extends ExtendedEntityRepository
{
    public function findLatestByVendingMachineSyncType($vendingMachine, $syncedType)
    {
        $query = $this->createQueryBuilder('vms')
            ->select('vms')
            ->where('vms.vendingMachine = :vendingMachine')
            ->andWhere('vms.syncedType = :syncedType')
            ->setParameters([
                'vendingMachine' => $vendingMachine,
                'syncedType'     => $syncedType
            ])
            ->orderBy('vms.syncedAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
        ;

        return $query->getOneOrNullResult();
    }
}