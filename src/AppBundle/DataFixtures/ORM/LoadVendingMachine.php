<?php
// AppBundle/DataFixtures/ORM/LoadVendingMachine.php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture,
    Doctrine\Common\DataFixtures\OrderedFixtureInterface,
    Doctrine\Common\Persistence\ObjectManager;

use AppBundle\Entity\VendingMachine\VendingMachine;

class LoadVendingMachine extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $vendingMachine_donatello = (new VendingMachine)
            ->setSchool(NULL)
            ->setProductVendingGroup(NULL)
            ->setName("Donatello")
            ->setCode("VM-0001")
            ->setNameTechnician("Splinter")
            ->setNumberShelves("5")
            ->setNumberSprings("5")
        ;
        $manager->persist($vendingMachine_donatello);

        $vendingMachine_leonardo = (new VendingMachine)
            ->setSchool(NULL)
            ->setProductVendingGroup(NULL)
            ->setName("Leonardo")
            ->setCode("VM-0002")
            ->setNameTechnician("Splinter")
            ->setNumberShelves("5")
            ->setNumberSprings("5")
        ;
        $manager->persist($vendingMachine_leonardo);

        $vendingMachine_michelangelo = (new VendingMachine)
            ->setSchool(NULL)
            ->setProductVendingGroup(NULL)
            ->setName("Michelangelo")
            ->setCode("VM-0003")
            ->setNameTechnician("Splinter")
            ->setNumberShelves("5")
            ->setNumberSprings("5")
        ;
        $manager->persist($vendingMachine_michelangelo);

        $vendingMachine_raphael = (new VendingMachine)
            ->setSchool(NULL)
            ->setProductVendingGroup(NULL)
            ->setName("Raphael")
            ->setCode("VM-0004")
            ->setNameTechnician("Splinter")
            ->setNumberShelves("5")
            ->setNumberSprings("5")
        ;
        $manager->persist($vendingMachine_raphael);

        $manager->flush();

        $this->addReference('vendingMachine_donatello', $vendingMachine_donatello);
        $this->addReference('vendingMachine_leonardo', $vendingMachine_leonardo);
        $this->addReference('vendingMachine_michelangelo', $vendingMachine_michelangelo);
        $this->addReference('vendingMachine_raphael', $vendingMachine_raphael);
    }

    public function getOrder()
    {
        return 7;
    }
}