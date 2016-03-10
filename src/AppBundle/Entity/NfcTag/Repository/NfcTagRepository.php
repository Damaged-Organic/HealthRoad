<?php
// AppBundle/Entity/NfcTag/Repository/NfcTagRepository.php
namespace AppBundle\Entity\NfcTag\Repository;

use AppBundle\Entity\Utility\Extended\ExtendedEntityRepository,
    AppBundle\Entity\VendingMachine\VendingMachine;

class NfcTagRepository extends ExtendedEntityRepository
{
    public function findAll()
    {
        $query = $this->createQueryBuilder('nt')
            ->select('nt, st')
            ->leftJoin('nt.student', 'st')
            ->getQuery()
        ;

        return $query->getResult();
    }

    public function findByNfcTagNumber(array $nfcTagNumbers)
    {
        $query = $this->createQueryBuilder('nt')
            ->select('nt, st')
            ->leftJoin('nt.student', 'st')
            ->where('nt.number IN (:nfcTagNumbers)')
            ->setParameter('nfcTagNumbers', $nfcTagNumbers)
            ->getQuery()
        ;

        return $query->getResult();
    }

    public function findByNfcTagNumberIndexedByNumber(array $nfcTagNumbers)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('nt, st')
            ->from('AppBundle:NfcTag\NfcTag', 'nt', 'nt.number')
            ->leftJoin('nt.student', 'st')
            ->where('nt.number IN (:nfcTagNumbers)')
            ->setParameter('nfcTagNumbers', $nfcTagNumbers)
            ->getQuery()
        ;

        return $query->getResult();
    }

    public function findAvailableByVendingMachine(VendingMachine $vendingMachine)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('nt, st')
            ->from('AppBundle:NfcTag\NfcTag', 'nt', 'nt.code')
            ->leftJoin('nt.student', 'st')
            ->where('st.school = :school')
            ->setParameter('school', $vendingMachine->getSchool())
            ->getQuery()
        ;

        return $query->getResult();
    }

    public function findAllIndexedByCode()
    {
        $query = $this->_em->createQueryBuilder()
            ->select('nt, st')
            ->from('AppBundle:NfcTag\NfcTag', 'nt', 'nt.code')
            ->leftJoin('nt.student', 'st')
            ->getQuery()
        ;

        return $query->getResult();
    }
}
