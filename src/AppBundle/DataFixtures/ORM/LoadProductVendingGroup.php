<?php
// AppBundle/DataFixtures/ORM/LoadVendingMachineGroup.php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture,
    Doctrine\Common\DataFixtures\OrderedFixtureInterface,
    Doctrine\Common\Persistence\ObjectManager;

use AppBundle\Entity\Product\ProductVendingGroup;

class LoadProductVendingGroup extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $productVendingGroup_0 = (new ProductVendingGroup)
            ->setName("VM Group 1")
        ;
        $manager->persist($productVendingGroup_0);

        $productVendingGroup_1 = (new ProductVendingGroup)
            ->setName("VM Group 2")
        ;
        $manager->persist($productVendingGroup_1);

        $manager->flush();
    }

    public function getOrder()
    {
        return 10;
    }
}