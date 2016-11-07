<?php
// src/AppBundle/DataFixtures/ORM/LoadBanknoteList.php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture,
    Doctrine\Common\DataFixtures\OrderedFixtureInterface,
    Doctrine\Common\Persistence\ObjectManager;

use AppBundle\Entity\Banknote\BanknoteList;

class LoadBanknoteList extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $banknoteList_1_1 = (new BanknoteList)
            ->setTransaction($this->getReference('transaction_1'))
            ->setBanknote($this->getReference('banknote_uah_1'))
            ->setQuantity(10)
        ;
        $manager->persist($banknoteList_1_1);

        // ---

        $banknoteList_1_2 = (new BanknoteList)
            ->setTransaction($this->getReference('transaction_1'))
            ->setBanknote($this->getReference('banknote_uah_50'))
            ->setQuantity(15)
        ;
        $manager->persist($banknoteList_1_2);

        // ---

        $banknoteList_2_1 = (new BanknoteList)
            ->setTransaction($this->getReference('transaction_2'))
            ->setBanknote($this->getReference('banknote_uah_200'))
            ->setQuantity(10)
        ;
        $manager->persist($banknoteList_2_1);

        // ---

        $banknoteList_2_2 = (new BanknoteList)
            ->setTransaction($this->getReference('transaction_2'))
            ->setBanknote($this->getReference('banknote_uah_500'))
            ->setQuantity(5)
        ;
        $manager->persist($banknoteList_2_2);

        // ---

        $manager->flush();
    }

    public function getOrder()
    {
        return 15;
    }
}
