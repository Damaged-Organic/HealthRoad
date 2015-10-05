<?php
// AppBundle/DataFixtures/ORM/LoadSettlement.php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture,
    Doctrine\Common\DataFixtures\OrderedFixtureInterface,
    Doctrine\Common\Persistence\ObjectManager;

use AppBundle\Entity\Settlement\Settlement;

class LoadSettlement extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $settlement_0 = (new Settlement)
            ->setRegion($this->getReference('region_0'))
            ->setName("Киев")
        ;
        $manager->persist($settlement_0);

        $settlement_1 = (new Settlement)
            ->setRegion($this->getReference('region_1'))
            ->setName("Белая Церковь")
        ;
        $manager->persist($settlement_1);

        $settlement_2 = (new Settlement)
            ->setRegion($this->getReference('region_2'))
            ->setName("Черкассы")
        ;
        $manager->persist($settlement_2);

        $manager->flush();

        $this->addReference('settlement_0', $settlement_0);
        $this->addReference('settlement_1', $settlement_1);
        $this->addReference('settlement_2', $settlement_2);
    }

    public function getOrder()
    {
        return 5;
    }
}