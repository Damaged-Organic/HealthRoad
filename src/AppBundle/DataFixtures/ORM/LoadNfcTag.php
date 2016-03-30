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
        for($s = 1; $s <= 1; $s++)
        {
            for ($i = 1; $i <= 100; $i++)
            {
                $number = "AA" . rand(100000, 999999);

                $nfc_{"{$s}_{$i}"} = (new NfcTag)
                    ->setStudent($this->getReference("kid_{$s}_{$i}"))
                    ->setNumber($number)
                    ->setCode(uniqid())
                ;
                $manager->persist($nfc_{"{$s}_{$i}"});
            }
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 12;
    }
}
