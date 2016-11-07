<?php
// src/AppBundle/DataFixtures/ORM/LoadBanknote.php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture,
    Doctrine\Common\DataFixtures\OrderedFixtureInterface,
    Doctrine\Common\Persistence\ObjectManager;

use AppBundle\Entity\Banknote\Banknote;

class LoadBanknote extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $banknote_uah_1 = (new Banknote)
            ->setCurrency(Banknote::BANKNOTE_CURRENCY_UAH)
            ->setNominal(1.00)
        ;
        $manager->persist($banknote_uah_1);

        // ---

        $banknote_uah_2 = (new Banknote)
            ->setCurrency(Banknote::BANKNOTE_CURRENCY_UAH)
            ->setNominal(2.00)
        ;
        $manager->persist($banknote_uah_2);

        // ---

        $banknote_uah_5 = (new Banknote)
            ->setCurrency(Banknote::BANKNOTE_CURRENCY_UAH)
            ->setNominal(5.00)
        ;
        $manager->persist($banknote_uah_5);

        // ---

        $banknote_uah_10 = (new Banknote)
            ->setCurrency(Banknote::BANKNOTE_CURRENCY_UAH)
            ->setNominal(10.00)
        ;
        $manager->persist($banknote_uah_10);

        // ---

        $banknote_uah_20 = (new Banknote)
            ->setCurrency(Banknote::BANKNOTE_CURRENCY_UAH)
            ->setNominal(20.00)
        ;
        $manager->persist($banknote_uah_20);

        // ---

        $banknote_uah_50 = (new Banknote)
            ->setCurrency(Banknote::BANKNOTE_CURRENCY_UAH)
            ->setNominal(50.00)
        ;
        $manager->persist($banknote_uah_50);

        // ---

        $banknote_uah_100 = (new Banknote)
            ->setCurrency(Banknote::BANKNOTE_CURRENCY_UAH)
            ->setNominal(100.00)
        ;
        $manager->persist($banknote_uah_100);

        // ---

        $banknote_uah_200 = (new Banknote)
            ->setCurrency(Banknote::BANKNOTE_CURRENCY_UAH)
            ->setNominal(200.00)
        ;
        $manager->persist($banknote_uah_200);

        // ---

        $banknote_uah_500 = (new Banknote)
            ->setCurrency(Banknote::BANKNOTE_CURRENCY_UAH)
            ->setNominal(500.00)
        ;
        $manager->persist($banknote_uah_500);

        // ---

        $this->addReference('banknote_uah_1', $banknote_uah_1);
        $this->addReference('banknote_uah_2', $banknote_uah_2);
        $this->addReference('banknote_uah_5', $banknote_uah_5);
        $this->addReference('banknote_uah_10', $banknote_uah_10);
        $this->addReference('banknote_uah_20', $banknote_uah_20);
        $this->addReference('banknote_uah_50', $banknote_uah_50);
        $this->addReference('banknote_uah_100', $banknote_uah_100);
        $this->addReference('banknote_uah_200', $banknote_uah_200);
        $this->addReference('banknote_uah_500', $banknote_uah_500);

        $manager->flush();
    }

    public function getOrder()
    {
        return 13;
    }
}
