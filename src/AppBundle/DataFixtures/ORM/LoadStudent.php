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
        for($s = 1; $s <= 1; $s++)
        {
            for ($i = 1; $i <= 100; $i++)
            {
                $kid_{"{$s}_{$i}"} = (new Student)
                    ->setSchool($this->getReference("school_{$s}"))
                    ->setName("Ученик {$i}")
                    ->setSurname("")
                    ->setPatronymic("")
                    ->setDateOfBirth(new DateTime)
                    ->setGender('male')
                    ->setTotalLimit(100.00)
                    ->setDailyLimit(50.00);
                $manager->persist($kid_{"{$s}_{$i}"});

                $this->addReference("kid_{$s}_{$i}", $kid_{"{$s}_{$i}"});
            }
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 11;
    }
}
