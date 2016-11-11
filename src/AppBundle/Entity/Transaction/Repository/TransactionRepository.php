<?php
// src/AppBundle/Entity/Transaction/Repository/TransactionRepository.php
namespace AppBundle\Entity\Transaction\Repository;

use AppBundle\Entity\Utility\Extended\ExtendedEntityRepository,
    AppBundle\Entity\Transaction\Transaction;

class TransactionRepository extends ExtendedEntityRepository
{
    // BEGIN: Extended find methods
    public function findChained()
    {
        $this->chain = $this->createQueryBuilder('ta')
            ->select('ta, nt, st, s, vm')
            ->leftJoin('ta.vendingMachine', 'vm')
            ->leftJoin('ta.nfcTag', 'nt')
            ->leftJoin('ta.student', 'st')
            ->leftJoin('st.school', 's')
            ->orderBy('ta.id', 'DESC')
        ;

        return $this;
    }

    public function chainFindBy(array $findBy)
    {
        $this->baseChainFindBy($findBy, 'ta');

        return $this;
    }

    public function chainSearchBy($searchBy)
    {
        $entityFields = [
            'ta.syncTransactionId',
            'vm.serial',
            'nt.number',
            'st.name', 'st.surname', 'st.patronymic',
        ];

        $this->baseChainSearchBy($searchBy, $entityFields);

        return $this;
    }
    // END: Extended find methods

    public function rawInsertTransaction(Transaction $transaction)
    {
        $conn = $this->getEntityManager()->getConnection();

        $queryArgs = [
            $transaction->getVendingMachine()->getId(),
            $transaction->getNfcTag()->getId(),
            $transaction->getStudent()->getId(),
            $transaction->getSyncTransactionId(),
            $transaction->getSyncTransactionAt()->format('Y-m-d H:i:s'),
            $transaction->getSyncNfcTagCode(),
            $transaction->getSyncStudentId(),
            $transaction->getVendingMachineSerial(),
            $transaction->getVendingMachineSyncId()
        ];
        $queryArgsNumber = count($queryArgs);

        if( !$queryArgs )
            return;

        $queryString = "
            INSERT INTO transactions (
                vending_machine_id,
                nfc_tag_id,
                student_id,
                sync_transaction_id,
                sync_transaction_at,
                sync_nfc_tag_code,
                sync_student_id,
                vending_machine_serial,
                vending_machine_sync_id
            ) VALUES (" . substr(str_repeat("?,", $queryArgsNumber), 0, -1) . ")"
        ;

        $stmt = $conn->prepare($queryString);

        $stmt->execute($queryArgs);

        return $conn->lastInsertId();
    }

    public function rawUpdateTransactionsTotalAmounts(array $transactionsArray)
    {
        $queryStringWhen = $queryStringIds = '';
        $queryArgsWhen = $queryArgsIds = $queryArgs = [];

        foreach( $transactionsArray as $transaction )
        {
            $queryStringWhen .= " WHEN ? THEN ? ";
            $queryStringIds  .= "?,";

            $queryArgsWhen = array_merge($queryArgsWhen, [
                $transaction->getId(),
                $transaction->getTotalAmount()
            ]);

            $queryArgsIds = array_merge($queryArgsIds, [
                $transaction->getId()
            ]);
        }

        $queryArgs = array_merge($queryArgsWhen, $queryArgsIds);

        if( !$queryArgs )
            return;

        $queryStringIds = substr($queryStringIds, 0, -1);

        $queryString = "
            UPDATE transactions
            SET total_amount =
            (CASE id {$queryStringWhen} END)
            WHERE id IN ({$queryStringIds})
        ";

        $stmt = $this->getEntityManager()->getConnection()->prepare($queryString);

        $stmt->execute($queryArgs);
    }
}
