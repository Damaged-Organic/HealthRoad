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
        for($i = 1; $i <= 13; $i++)
        {
            $school_{$i} = (new School)
                ->setSettlement($this->getReference('settlement_0'))
                ->setName("Школа №{$i}")
                ->setAddress("Офис")
            ;
            $manager->persist($school_{$i});

            $this->addReference("school_{$i}", $school_{$i});
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 6;
    }
}