<?php
// src/AppBundle/DataFixtures/ORM/LoadTransaction.php
namespace AppBundle\DataFixtures\ORM;

use DateTime;

use Doctrine\Common\DataFixtures\AbstractFixture,
    Doctrine\Common\DataFixtures\OrderedFixtureInterface,
    Doctrine\Common\Persistence\ObjectManager;

use AppBundle\Entity\Transaction\Transaction;

class LoadTransaction extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $transaction_1 = (new Transaction)
            ->setVendingMachine($this->getReference('vendingMachine_donatello'))
            ->setStudent($this->getReference('kid_1_1'))
            ->setNfcTag(NULL)
            ->setSyncTransactionId('123456')
            ->setSyncTransactionAt(new DateTime)
            ->setSyncNfcTagCode('qwe123')
            ->setVendingMachineSerial('tstboard-0001')
            ->setVendingMachineSyncId('123456')
        ;
        $manager->persist($transaction_1);

        // ---

        $transaction_2 = (new Transaction)
            ->setVendingMachine($this->getReference('vendingMachine_donatello'))
            ->setStudent($this->getReference('kid_1_2'))
            ->setNfcTag(NULL)
            ->setSyncTransactionId('654321')
            ->setSyncTransactionAt(new DateTime)
            ->setSyncNfcTagCode('asd123')
            ->setVendingMachineSerial('tstboard-0001')
            ->setVendingMachineSyncId('654321')
        ;
        $manager->persist($transaction_2);

        // ---

        $this->addReference('transaction_1', $transaction_1);
        $this->addReference('transaction_2', $transaction_2);

        $manager->flush();
    }

    public function getOrder()
    {
        return 14;
    }
}
