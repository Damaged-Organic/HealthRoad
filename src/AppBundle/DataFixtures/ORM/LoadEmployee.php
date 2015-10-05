<?php
// AppBundle/DataFixtures/ORM/LoadEmployee.php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture,
    Doctrine\Common\DataFixtures\OrderedFixtureInterface,
    Doctrine\Common\Persistence\ObjectManager;

use AppBundle\Entity\Employee\Employee;

class LoadEmployee extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $employee_1 = (new Employee)
            ->setEmployeeGroup($this->getReference('superadministrator'))
            ->setUsername("Superanonymous")
            ->setPassword('$2y$15$ZINZ7dY.USDTyhWwfLvlB.476X.6kcOZA0OVh08fUqGThpAWm9Xrq')
            ->setName("...")
            ->setSurname("...")
            ->setPatronymic("...")
            ->setEmail("grimv01k@gmail.com")
            ->setPhoneNumber("+38 (000) 000-00-00")
            ->setSkypeName("grimv01k@live.com")
        ;
        $manager->persist($employee_1);

        // ---

        $employee_2 = (new Employee)
            ->setEmployeeGroup($this->getReference('administrator'))
            ->setUsername("Anonymous")
            ->setPassword('$2y$15$ZINZ7dY.USDTyhWwfLvlB.476X.6kcOZA0OVh08fUqGThpAWm9Xrq')
            ->setName("...")
            ->setSurname("...")
            ->setPatronymic("...")
            ->setEmail("grimv01k@gmail.com")
            ->setPhoneNumber("+38 (000) 000-00-00")
            ->setSkypeName("grimv01k@live.com")
        ;
        $manager->persist($employee_2);

        // ---

        $employee_3 = (new Employee)
            ->setEmployeeGroup($this->getReference('registrar'))
            ->setUsername("Registronymous")
            ->setPassword('$2y$15$ZINZ7dY.USDTyhWwfLvlB.476X.6kcOZA0OVh08fUqGThpAWm9Xrq')
            ->setName("...")
            ->setSurname("...")
            ->setPatronymic("...")
            ->setEmail("grimv01k@gmail.com")
            ->setPhoneNumber("+38 (000) 000-00-00")
            ->setSkypeName("grimv01k@live.com")
        ;
        $manager->persist($employee_3);

        // ---

        $manager->flush();

        $this->addReference('registronymous', $employee_3);
    }

    public function getOrder()
    {
        return 3;
    }
}