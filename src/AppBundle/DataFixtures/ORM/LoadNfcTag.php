<?php
// AppBundle/DataFixtures/ORM/LoadNfcTag.php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture,
    Doctrine\Common\DataFixtures\OrderedFixtureInterface,
    Doctrine\Common\Persistence\ObjectManager;

use AppBundle\Entity\NfcTag\NfcTag;

class LoadNfcTag extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $nfcTag_1 = (new NfcTag)
            ->setNumber("AA123456")
            ->setCode("q1w2e3r4t5y6u71")
            //->setVendingMachine($this->getReference('vendingMachine_donatello'))
            ->setStudent($this->getReference('kid_1'))
        ;
        $manager->persist($nfcTag_1);

        // ---

        $nfcTag_2 = (new NfcTag)
            ->setNumber("BB123456")
            ->setCode("q1w2e3r4t5y6u72")
            //->setVendingMachine($this->getReference('vendingMachine_donatello'))
            ->setStudent($this->getReference('kid_2'))
        ;
        $manager->persist($nfcTag_2);

        // ---

        $nfcTag_3 = (new NfcTag)
            ->setNumber("CC123456")
            ->setCode("q1w2e3r4t5y6u73")
            //->setVendingMachine($this->getReference('vendingMachine_donatello'))
            ->setStudent($this->getReference('kid_3'))
        ;
        $manager->persist($nfcTag_3);

        // ---

        $manager->flush();

        $this->addReference('nfc_tag_1', $nfcTag_1);
        $this->addReference('nfc_tag_2', $nfcTag_2);
        $this->addReference('nfc_tag_3', $nfcTag_3);
    }

    public function getOrder()
    {
        return 12;
    }
}