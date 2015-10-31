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
            ->setUsername("Superadmin")
            ->setPassword('$2y$15$T3cF.FdR2q2I/kureAXbI.ahZDXl.Tu.81j4UsG9krMWLh1XvLo.O')
            ->setName("")
            ->setSurname("")
            ->setPatronymic("")
            ->setEmail("grimv01k@gmail.com")
            ->setPhoneNumber("+38 (000) 000-00-00")
            ->setSkypeName("grimv01k@live.com")
        ;
        $manager->persist($employee_1);

        // ---

        $employee_2 = (new Employee)
            ->setEmployeeGroup($this->getReference('administrator'))
            ->setUsername("Admin")
            ->setPassword('$2y$15$T3cF.FdR2q2I/kureAXbI.ahZDXl.Tu.81j4UsG9krMWLh1XvLo.O')
            ->setName("")
            ->setSurname("")
            ->setPatronymic("")
            ->setEmail("grimv01k@gmail.com")
            ->setPhoneNumber("+38 (000) 000-00-00")
            ->setSkypeName("grimv01k@live.com")
        ;
        $manager->persist($employee_2);

        // ---

        $employee_3 = (new Employee)
            ->setEmployeeGroup($this->getReference('manager'))
            ->setUsername("Manager")
            ->setPassword('$2y$15$T3cF.FdR2q2I/kureAXbI.ahZDXl.Tu.81j4UsG9krMWLh1XvLo.O')
            ->setName("")
            ->setSurname("")
            ->setPatronymic("")
            ->setEmail("grimv01k@gmail.com")
            ->setPhoneNumber("+38 (000) 000-00-00")
            ->setSkypeName("grimv01k@live.com")
        ;
        $manager->persist($employee_3);

        // ---

        $employee_4 = (new Employee)
            ->setEmployeeGroup($this->getReference('registrar'))
            ->setUsername("Registrar")
            ->setPassword('$2y$15$T3cF.FdR2q2I/kureAXbI.ahZDXl.Tu.81j4UsG9krMWLh1XvLo.O')
            ->setName("")
            ->setSurname("")
            ->setPatronymic("")
            ->setEmail("grimv01k@gmail.com")
            ->setPhoneNumber("+38 (000) 000-00-00")
            ->setSkypeName("grimv01k@live.com")
        ;
        $manager->persist($employee_4);

        // ---

        $manager->flush();

        $this->addReference('registronymous', $employee_4);
    }

    public function getOrder()
    {
        return 3;
    }
}