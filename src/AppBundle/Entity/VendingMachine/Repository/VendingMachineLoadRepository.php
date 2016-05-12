<?php
// AppBundle/Entity/VendingMachine/Repository/VendingMachineLoadRepository.php
namespace AppBundle\Entity\VendingMachine\Repository;

use AppBundle\Entity\Utility\Extended\ExtendedEntityRepository,
    AppBundle\Entity\VendingMachine\VendingMachine;

class VendingMachineLoadRepository extends ExtendedEntityRepository
{
    // BEGIN: Extended find methods
    public function findChained()
    {
        $this->chain = $this->createQueryBuilder('vml')
            ->select('vml, vm')
            ->leftJoin('vml.vendingMachine', 'vm')
        ;

        return $this;
    }

    public function chainFindBy(array $findBy)
    {
        $this->baseChainFindBy($findBy, 'vml');

        return $this;
    }

    public function chainSearchBy($searchBy)
    {
        $entityFields = [
            'vm.serial',
        ];

        $this->baseChainSearchBy($searchBy, $entityFields);

        return $this;
    }
    // END: Extended find methods

    public function rawDeleteVendingMachineLoad(VendingMachine $vendingMachine)
    {
        $builder = $this->_em->createQueryBuilder();

        $query = $builder
            ->delete('AppBundle:VendingMachine\VendingMachineLoad', 'vml')
            ->where($builder->expr()->eq('vml.vendingMachine', ':vendingMachine'))
            ->setParameter('vendingMachine', $vendingMachine)
            ->getQuery()
        ;

        return $query->execute();
    }

    public function rawInsertVendingMachineLoad(array $vendingMachineLoadArray)
    {
        $queryString = '';

        foreach( $vendingMachineLoadArray as $load )
        {
            $queryString .= " (
                '{$load->getVendingMachine()->getId()}',
                '{$load->getProductId()}',
                '{$load->getLoadedAt()}',
                '{$load->getProductQuantity()}',
                '{$load->getSpringPosition()}'
            ),";
        }

        $queryString = substr($queryString, 0, -1);

        $queryString = "
            INSERT INTO vending_machines_load (
                vending_machine_id,
                product_id,
                loaded_at,
                product_quantity,
                spring_position
            ) VALUES " . $queryString;

        $stmt = $this->getEntityManager()->getConnection()->prepare($queryString);

        $stmt->execute();
    }
}
