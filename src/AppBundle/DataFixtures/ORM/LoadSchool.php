<?php
// AppBundle/DataFixtures/ORM/LoadSchool.php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture,
    Doctrine\Common\DataFixtures\OrderedFixtureInterface,
    Doctrine\Common\Persistence\ObjectManager;

use AppBundle\Entity\School\School;

class LoadSchool extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $school_0 = (new School)
            ->setSettlement($this->getReference('settlement_0'))
            ->setName("Лицей 1")
            ->setAddress("Full address")
        ;
        $manager->persist($school_0);

        $school_1 = (new School)
            ->setSettlement($this->getReference('settlement_1'))
            ->setName("СЗОШ 5")
            ->setAddress("Full address")
        ;
        $manager->persist($school_1);

        $school_2 = (new School)
            ->setSettlement($this->getReference('settlement_2'))
            ->setName("СЗОШ 666")
            ->setAddress("Full address")
        ;
        $manager->persist($school_2);

        $manager->flush();

        $this->addReference('school_0', $school_0);
        $this->addReference('school_1', $school_1);
        $this->addReference('school_2', $school_2);
    }

    public function getOrder()
    {
        return 6;
    }
}