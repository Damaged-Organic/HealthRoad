<?php
// src/AppBundle/Entity/Banknote/Repository/BanknoteListRepository.php
namespace AppBundle\Entity\Banknote\Repository;

use AppBundle\Entity\Utility\Extended\ExtendedEntityRepository,
    AppBundle\Entity\Banknote\BanknoteList;

class BanknoteListRepository extends ExtendedEntityRepository
{
    // BEGIN: Extended find methods
    public function findChained()
    {
        $this->chain = $this->createQueryBuilder('bl')
            ->select('bl, b, ta')
            ->leftJoin('bl.banknote', 'b')
            ->leftJoin('bl.transaction', 'ta')
        ;

        return $this;
    }

    public function chainFindBy(array $findBy)
    {
        $this->baseChainFindBy($findBy, 'bl');

        return $this;
    }

    public function chainSearchBy($searchBy)
    {
        $entityFields = [
            'bl.id',
        ];

        $this->baseChainSearchBy($searchBy, $entityFields);

        return $this;
    }
    // END: Extended find methods

    public function rawInsertBanknoteLists(array $banknoteLists)
    {
        $queryString = '';
        $queryArgs   = [];

        foreach( $banknoteLists as $banknoteList )
        {
            if( $banknoteList instanceof BanknoteList )
            {
                $boundTokens = [
                    $banknoteList->getTransaction()->getId(),
                    $banknoteList->getBanknote()->getId(),
                    $banknoteList->getQuantity()
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
            INSERT INTO banknotes_lists (
                transaction_id,
                banknote_id,
                quantity
            ) VALUES " . $queryString
        ;

        $stmt = $this->getEntityManager()->getConnection()->prepare($queryString);

        $stmt->execute($queryArgs);
    }
}
