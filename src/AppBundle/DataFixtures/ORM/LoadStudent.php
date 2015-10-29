<?php
// AppBundle/DataFixtures/ORM/LoadStudent.php
namespace AppBundle\DataFixtures\ORM;

use DateTime;

use Doctrine\Common\DataFixtures\AbstractFixture,
    Doctrine\Common\DataFixtures\OrderedFixtureInterface,
    Doctrine\Common\Persistence\ObjectManager;

use AppBundle\Entity\Student\Student;

class LoadStudent extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $kid_1 = (new Student)
            ->setEmployee($this->getReference('registronymous'))
            ->setName("KidName 1")
            ->setSurname("KidSurname 1")
            ->setPatronymic("KidPatronymic 1")
            ->setDateOfBirth(new DateTime)
            ->setGender('male')
            ->setTotalLimit(150.00)
            ->setDailyLimit(50.00)
        ;
        $manager->persist($kid_1);

        // ---

        $kid_2 = (new Student)
            ->setEmployee($this->getReference('registronymous'))
            ->setName("KidName 2")
            ->setSurname("KidSurname 2")
            ->setPatronymic("KidPatronymic 2")
            ->setDateOfBirth(new DateTime)
            ->setGender('female')
            ->setTotalLimit(140.00)
            ->setDailyLimit(40.00)
        ;
        $manager->persist($kid_2);

        // ---

        $kid_3 = (new Student)
            ->setEmployee($this->getReference('registronymous'))
            ->setName("KidName 3")
            ->setSurname("KidSurname 3")
            ->setPatronymic("KidPatronymic 3")
            ->setDateOfBirth(new DateTime)
            ->setGender('male')
            ->setTotalLimit(130.00)
            ->setDailyLimit(30.00)
        ;
        $manager->persist($kid_3);

        // ---

        $manager->flush();

        $this->addReference('kid_1', $kid_1);
        $this->addReference('kid_2', $kid_2);
        $this->addReference('kid_3', $kid_3);
    }

    public function getOrder()
    {
        return 11;
    }
}