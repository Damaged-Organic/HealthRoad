<?php
// AppBundle/Entity/NfcTag/Repository/NfcTagRepository.php
namespace AppBundle\Entity\NfcTag\Repository;

use AppBundle\Entity\Utility\Extended\ExtendedEntityRepository;
use Doctrine\ORM\Query;

class NfcTagRepository extends ExtendedEntityRepository
{
    public function findByVendingMachine($vendingMachine)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('nt, s')
            ->from('AppBundle:NfcTag\NfcTag', 'nt', 'nt.code')
            ->leftJoin('nt.student', 's')
            ->where('nt.vendingMachine = :vendingMachine')
            ->setParameter('vendingMachine', $vendingMachine)
            ->getQuery()
        ;

        return $query->getResult();
    }
}