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
            ->setPhoneNumber("+38 (000) 000-00-00")
            ->setPassword('$2y$12$KKKdwzMFY9KnRif79Eb.DeZPyPYJCjsFteSCawG3C32k6l6hMNuIe')
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
        return 4;
    }
}