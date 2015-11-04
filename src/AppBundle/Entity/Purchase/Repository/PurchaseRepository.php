<?php
// AppBundle/Entity/Purchase/Repository/PurchaseRepository.php
namespace AppBundle\Entity\Purchase\Repository;

use AppBundle\Entity\Utility\Extended\ExtendedEntityRepository,
    AppBundle\Entity\Purchase\Purchase;

class PurchaseRepository extends ExtendedEntityRepository
{
    public function findGroupedBySchoolByDate($yesterdayDate)
    {
        $query = $this->createQueryBuilder('p')
            ->select('p, pr, prc, SUM(pr.price) AS purchaseSum, COUNT(pr.id) AS purchaseAmount')
            ->leftJoin('p.product', 'pr')
            ->leftJoin('pr.productCategory', 'prc')
            ->andWhere('p.syncPurchasedAt > :yesterdayDateStart')
            ->andWhere('p.syncPurchasedAt < :yesterdayDateEnd')
            ->setParameter('yesterdayDateStart', "{$yesterdayDate} 00:00:00")
            ->setParameter('yesterdayDateEnd', "{$yesterdayDate} 23:59:59")
            ->groupBy('pr.id')
            ->getQuery()
        ;

        return $query->getResult();
    }

    public function findSumsByStudentsWithSyncId($syncId)
    {
        $query = $this->createQueryBuilder('p')
            ->select('nt.code, SUM(pr.price) as price_sum')
            ->leftJoin('p.nfcTag', 'nt')
            ->leftJoin('p.product', 'pr')
            ->where('p.vendingMachineSyncId = :syncId')
            ->setParameter('syncId', $syncId)
            ->groupBy('nt.code')
            ->getQuery()
        ;

        return $query->getResult();
    }

    public function rawInsertPurchases(array $purchasesArray)
    {
        $queryString = '';

        foreach( $purchasesArray as $purchase )
        {
            if( $purchase instanceof Purchase )
            {
                $queryString .= " (
                    '{$purchase->getVendingMachine()->getId()}',
                    '{$purchase->getProduct()->getId()}',
                    '{$purchase->getNfcTag()->getId()}',
                    '{$purchase->getSyncPurchaseId()}',
                    '{$purchase->getSyncNfcTagCode()}',
                    '{$purchase->getSyncProductId()}',
                    '{$purchase->getSyncProductPrice()}',
                    '{$purchase->getSyncPurchasedAt()->format('Y-m-d H:i:s')}',
                    '{$purchase->getVendingMachineSerial()}',
                    '{$purchase->getVendingMachineSyncId()}'
                ),";
            }
        }

        if( !$queryString )
            return;

        $queryString = substr($queryString, 0, -1);

        $queryString = "
            INSERT INTO purchases (
                vending_machine_id,
                product_id,
                nfc_tag_id,
                sync_purchase_id,
                sync_nfc_tag_code,
                sync_product_id,
                sync_product_price,
                sync_purchased_at,
                vending_machine_serial,
                vending_machine_sync_id
            ) VALUES " . $queryString
        ;

        $stmt = $this->getEntityManager()->getConnection()->prepare($queryString);

        $stmt->execute();
    }
}