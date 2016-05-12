<?php
// AppBundle/Entity/Product/Repository/ProductRepository.php
namespace AppBundle\Entity\Product\Repository;

use AppBundle\Entity\Utility\Extended\ExtendedEntityRepository,
    AppBundle\Entity\Student\Student;

class ProductRepository extends ExtendedEntityRepository
{
    // BEGIN: Extended find methods
    public function findChained()
    {
        $this->chain = $this->createQueryBuilder('p')
            ->select('p, pc, pi, sp')
            ->leftJoin('p.productCategory', 'pc')
            ->leftJoin('p.productImages', 'pi')
            ->leftJoin('p.supplier', 'sp')
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
            'p.nameFull', 'p.code', 'pc.name', 'sp.name',
        ];

        $this->baseChainSearchBy($searchBy, $entityFields);

        return $this;
    }
    // END: Extended find methods

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

    public function findAvailableAndAllowedByStudent(Student $student)
    {
        $productIds = function(Student $student)
        {
            $productIds = [];

            foreach( $student->getProducts() as $product )
                $productIds[] = $product->getId();

            return implode(',', $productIds);
        };

        $builder = $this->_em->createQueryBuilder();

        $query = $this->_em->createQueryBuilder()
            ->select('p')
            ->from('AppBundle:Product\Product', 'p')
            ->leftJoin('p.productVendingGroups', 'pvg')
            ->leftJoin('pvg.vendingMachines', 'vm')
            ->where('vm.school = :school')
        ;

        if( $productIds = $productIds($student) )
            $query->andWhere($builder->expr()->notIn('p.id', $productIds));

        $query = $query
            ->setParameter('school', $student->getSchool())
            ->getQuery()
        ;

        return $query->getResult();
    }
}
