<?php
// src/AppBundle/Entity/Payment/Repository/PaymentReceiptRepository.php
namespace AppBundle\Entity\Payment\Repository;

use AppBundle\Entity\Utility\Extended\ExtendedEntityRepository,
    AppBundle\Entity\Payment\PaymentReceipt;

class PaymentReceiptRepository extends ExtendedEntityRepository
{
    public function findByChecksumHash(array $checksumHashes)
    {
        $query = $this->createQueryBuilder('pr')
            ->select('pr')
            ->where('pr.checksumHash IN (:checksumHashes)')
            ->setParameter('checksumHashes', $checksumHashes)
            ->getQuery()
        ;

        return $query->getResult();
    }

    public function rawInsertPaymentReceipts(array $paymentReceiptsArray)
    {
        $queryString = '';
        $queryArgs   = [];

        foreach( $paymentReceiptsArray as $paymentReceipt )
        {
            if( $paymentReceipt instanceof PaymentReceipt )
            {
                $queryString .= "(" . substr(str_repeat("?,", 17), 0, -1) . "),";

                $queryArgs = array_merge($queryArgs, [
                    $paymentReceipt->getNfcTag()->getId(),
                    $paymentReceipt->getStudent()->getId(),
                    $paymentReceipt->getReceiptNumber(),
                    $paymentReceipt->getReceiptDate()->format('Y-m-d H:i:s'),
                    $paymentReceipt->getDocumentNumber(),
                    $paymentReceipt->getOperationalDate()->format('Y-m-d H:i:s'),
                    $paymentReceipt->getNfcTagNumber(),
                    $paymentReceipt->getPayerFullName(),
                    $paymentReceipt->getPaymentPurpose(),
                    $paymentReceipt->getPaymentAmount(),
                    $paymentReceipt->getPaymentComission(),
                    $paymentReceipt->getPaymentNumbers(),
                    $paymentReceipt->getPaymentAmountTotal(),
                    $paymentReceipt->getPaymentComissionTotal(),
                    $paymentReceipt->getResultAmount(),
                    $paymentReceipt->getProfit(),
                    $paymentReceipt->getChecksumHash()
                ]);
            }
        }

        if( !$queryArgs )
            return;

        $queryString = substr($queryString, 0, -1);

        $queryString = "
            INSERT INTO payments_receipts (
                nfc_tag_id,
                student_id,
                receipt_number,
                receipt_date,
                document_number,
                operational_date,
                nfc_tag_number,
                payer_full_name,
                payment_purpose,
                payment_amount,
                payment_comission,
                payment_numbers,
                payment_amount_total,
                payment_comission_total,
                result_amount,
                profit,
                checksum_hash
            ) VALUES " . $queryString . " ON DUPLICATE KEY UPDATE checksum_hash=checksum_hash"
        ;

        $stmt = $this->getEntityManager()->getConnection()->prepare($queryString);

        $stmt->execute($queryArgs);
    }
}
