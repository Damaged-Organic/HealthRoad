<?php
// AppBundle/DataFixtures/ORM/LoadRegion.php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture,
    Doctrine\Common\DataFixtures\OrderedFixtureInterface,
    Doctrine\Common\Persistence\ObjectManager;

use AppBundle\Entity\Region\Region;

class LoadRegion extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $region_0 = (new Region)
            ->setName("Киев")
        ;
        $manager->persist($region_0);

        $region_1 = (new Region)
            ->setName("Киевская область")
        ;
        $manager->persist($region_1);

        $region_2 = (new Region)
            ->setName("Черкасская область")
        ;
        $manager->persist($region_2);

        $manager->flush();

        $this->addReference('region_0', $region_0);
        $this->addReference('region_1', $region_1);
        $this->addReference('region_2', $region_2);
    }

    public function getOrder()
    {
        return 4;
    }
}