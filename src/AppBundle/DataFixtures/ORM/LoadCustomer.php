<?php
// AppBundle/DataFixtures/ORM/LoadCustomer.php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture,
    Doctrine\Common\DataFixtures\OrderedFixtureInterface,
    Doctrine\Common\Persistence\ObjectManager;

use AppBundle\Entity\Customer\Customer;

class LoadCustomer extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $customer_1 = (new Customer)
            ->setEmployee($this->getReference('registronymous'))
            ->setPhoneNumber("+38 (001) 001-01-01")
            ->setPassword('$2a$12$YS2ChuzW89SzIfFjzeX6IunRl98Y7PMhZC8Hm6S1cEiuavQg1sDKG')
            ->setName("Name")
            ->setSurname("Surname")
            ->setPatronymic("Patronymic")
            ->setEmail("customer@gmail.com")
        ;
        $manager->persist($customer_1);

        // ---

        $manager->flush();
    }

    public function getOrder()
    {
        return 3;
    }
}