<?php
// AppBundle/DataFixtures/ORM/LoadEmployeeGroup.php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture,
    Doctrine\Common\DataFixtures\OrderedFixtureInterface,
    Doctrine\Common\Persistence\ObjectManager;

use AppBundle\Entity\Employee\EmployeeGroup;

class LoadEmployeeGroup extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $employeeGroup_1 = (new EmployeeGroup)
            ->setName("Superadministrator")
            ->setRole("ROLE_SUPERADMIN")
        ;
        $manager->persist($employeeGroup_1);

        $employeeGroup_2 = (new EmployeeGroup)
            ->setName("Administrator")
            ->setRole("ROLE_ADMIN")
        ;
        $manager->persist($employeeGroup_2);

        $employeeGroup_3 = (new EmployeeGroup)
            ->setName("Manager")
            ->setRole("ROLE_MANAGER")
        ;
        $manager->persist($employeeGroup_3);

        $employeeGroup_4 = (new EmployeeGroup)
            ->setName("Registrar")
            ->setRole("ROLE_REGISTRAR")
        ;
        $manager->persist($employeeGroup_4);

        $employeeGroup_5 = (new EmployeeGroup)
            ->setName("Accountant")
            ->setRole("ROLE_ACCOUNTANT")
        ;
        $manager->persist($employeeGroup_5);

        $manager->flush();

        $this->addReference('superadministrator', $employeeGroup_1);
        $this->addReference('administrator', $employeeGroup_2);
        $this->addReference('manager', $employeeGroup_3);
        $this->addReference('registrar', $employeeGroup_4);
        $this->addReference('accountant', $employeeGroup_5);
    }

    public function getOrder()
    {
        return 2;
    }
}