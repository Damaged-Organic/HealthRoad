<?php
// AppBundle/Entity/NfcTag/Repository/NfcTagRepository.php
namespace AppBundle\Entity\NfcTag\Repository;

use AppBundle\Entity\Utility\Extended\ExtendedEntityRepository,
    AppBundle\Entity\VendingMachine\VendingMachine;

class NfcTagRepository extends ExtendedEntityRepository
{
    public function findAvailableByVendingMachine(VendingMachine $vendingMachine)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('nt')
            ->from('AppBundle:NfcTag\NfcTag', 'nt', 'nt.code')
            ->leftJoin('nt.student', 'st')
            ->where('st.school = :school')
            ->setParameter('school', $vendingMachine->getSchool())
            ->getQuery()
        ;

        return $query->getResult();
    }
}