<?php
// AppBundle/Entity/Purchase/Repository/PurchaseRepository.php
namespace AppBundle\Entity\Purchase\Repository;

use AppBundle\Entity\Utility\Extended\ExtendedEntityRepository,
    AppBundle\Entity\Purchase\Purchase,
    AppBundle\Entity\VendingMachine\VendingMachine;

class PurchaseRepository extends ExtendedEntityRepository
{
    // BEGIN: Extended find methods
    public function findChained()
    {
        $this->chain = $this->createQueryBuilder('p')
            ->select('p, pr, nt, st, s, vm')
            ->leftJoin('p.product', 'pr')
            ->leftJoin('p.nfcTag', 'nt')
            ->leftJoin('p.student', 'st')
            ->leftJoin('st.school', 's')
            ->leftJoin('s.vendingMachines', 'vm')
            ->orderBy('p.id', 'DESC')
        ;

        return $this;
    }

    public function chainFindBy(array $findBy)
    {
        $this->baseChainFindBy($findBy, 'p');

        return $this;
    }

    public function chainSearchBy($searchBy)
    {
        $entityFields = [
            'pr.nameFull',
            'vm.serial',
            'nt.number',
            'st.name', 'st.surname', 'st.patronymic',
        ];

        $this->baseChainSearchBy($searchBy, $entityFields);

        return $this;
    }
    // END: Extended find methods

    public function findByDateGrouped($date)
    {
        $query = $this->createQueryBuilder('p')
            ->select('p, pr, prc, SUM(pr.price) AS purchaseSum, COUNT(pr.id) AS purchaseAmount')
            ->leftJoin('p.product', 'pr')
            ->leftJoin('pr.productCategory', 'prc')
            ->where('p.syncPurchasedAt >= :dateStart')
            ->andWhere('p.syncPurchasedAt < :dateEnd')
            ->setParameters([
                'dateStart' => "{$date} 00:00:00",
                'dateEnd'   => "{$date} 23:59:59"
            ])
            ->groupBy('pr.id')
            ->getQuery()
        ;

        return $query->getResult();
    }

    public function findByLoadDateGrouped()
    {
        $query = $this->createQueryBuilder('p')
            ->select('p, pr, prc, vm, SUM(pr.price) AS purchaseSum, COUNT(pr.id) AS purchaseAmount')
            ->leftJoin('p.product', 'pr')
            ->leftJoin('pr.productCategory', 'prc')
            ->leftJoin('p.vendingMachine', 'vm')
            ->where('vm.vendingMachineLoadedAt IS NOT NULL')
            ->andWhere('p.syncPurchasedAt >= vm.vendingMachineLoadedAt')
            ->groupBy('pr.id')
            ->getQuery()
        ;

        return $query->getResult();
    }

    public function findSumsByStudentsWithSyncId(VendingMachine $vendingMachine, $syncId)
    {
        $query = $this->createQueryBuilder('p')
            ->select('nt.code, SUM(pr.price) as price_sum')
            ->leftJoin('p.nfcTag', 'nt')
            ->leftJoin('p.product', 'pr')
            ->where('p.vendingMachine = :vendingMachine')
            ->andWhere('p.vendingMachineSyncId = :syncId')
            ->setParameters([
                'vendingMachine' => $vendingMachine,
                'syncId'         => $syncId
            ])
            ->groupBy('nt.code')
            ->getQuery()
        ;

        return $query->getResult();
    }

    public function rawInsertPurchases(array $purchasesArray)
    {
        $queryString = '';
        $queryArgs   = [];

        foreach( $purchasesArray as $purchase )
        {
            if( $purchase instanceof Purchase )
            {
                $boundTokens = [
                    $purchase->getVendingMachine()->getId(),
                    $purchase->getProduct()->getId(),
                    $purchase->getNfcTag()->getId(),
                    $purchase->getStudent()->getId(),
                    $purchase->getSyncPurchaseId(),
                    $purchase->getSyncNfcTagCode(),
                    $purchase->getSyncProductId(),
                    $purchase->getSyncProductPrice(),
                    $purchase->getSyncPurchasedAt()->format('Y-m-d H:i:s'),
                    $purchase->getVendingMachineSerial(),
                    $purchase->getVendingMachineSyncId()
                ];
                $boundTokensNumber = count($boundTokens);

                $queryString .= "(" . substr(str_repeat("?,", $boundTokensNumber), 0, -1) . "),";
                $queryArgs    = array_merge($queryArgs, $boundTokens);
            }
        }

        if( !$queryArgs )
            return;

        $queryString = substr($queryString, 0, -1);

        $queryString = "
            INSERT INTO purchases (
                vending_machine_id,
                product_id,
                nfc_tag_id,
                student_id,
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

        $stmt->execute($queryArgs);
    }
}
