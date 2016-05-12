<?php
// AppBundle/Entity/NfcTag/Repository/NfcTagRepository.php
namespace AppBundle\Entity\NfcTag\Repository;

use AppBundle\Entity\Utility\Extended\ExtendedEntityRepository,
    AppBundle\Entity\VendingMachine\VendingMachine;

class NfcTagRepository extends ExtendedEntityRepository
{
    // BEGIN: Extended find methods
    public function findChained()
    {
        $this->chain = $this->createQueryBuilder('nt')
            ->select('nt, st, s')
            ->leftJoin('nt.student', 'st')
            ->leftJoin('st.school', 's')
        ;

        return $this;
    }

    public function chainFindBy(array $findBy)
    {
        $this->baseChainFindBy($findBy, 'nt');

        return $this;
    }

    public function chainSearchBy($searchBy)
    {
        $entityFields = [
            'nt.number', 'nt.code',
            'st.name', 'st.surname', 'st.patronymic',
            's.name', 's.address',
        ];

        $this->baseChainSearchBy($searchBy, $entityFields);

        return $this;
    }
    // END: Extended find methods

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
