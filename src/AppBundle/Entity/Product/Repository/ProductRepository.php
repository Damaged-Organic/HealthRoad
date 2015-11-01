<?php
// AppBundle/Entity/Product/Repository/ProductRepository.php
namespace AppBundle\Entity\Product\Repository;

use AppBundle\Entity\Utility\Extended\ExtendedEntityRepository,
    AppBundle\Entity\Student\Student;

class ProductRepository extends ExtendedEntityRepository
{
    public function findAvailableByStudent(Student $student)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('p')
            ->from('AppBundle:Product\Product', 'p')
            ->leftJoin('p.productVendingGroups', 'pvg')
            ->leftJoin('pvg.vendingMachines', 'vm')
            ->where('vm.school = :school')
            ->setParameter('school', $student->getSchool())
            ->getQuery()
        ;

        return $query->getResult();
    }
}