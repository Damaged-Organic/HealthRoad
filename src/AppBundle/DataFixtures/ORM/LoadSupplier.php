<?php
// AppBundle/DataFixtures/ORM/LoadSupplier.php
namespace AppBundle\DataFixtures\ORM;

use DateTime;

use Doctrine\Common\DataFixtures\AbstractFixture,
    Doctrine\Common\DataFixtures\OrderedFixtureInterface,
    Doctrine\Common\Persistence\ObjectManager;

use AppBundle\Entity\Supplier\Supplier;

class LoadSupplier extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $supplier_0 = (new Supplier)
            ->setName("Sup 1")
            ->setNameLegal("Legal Sup 1")
            ->setDescription("Description")
            ->setLogoName("some_logo.jpg")
            ->setPhoneNumberSupplier("+38 (011) 111-11-11")
            ->setEmailSupplier("supplier@gmail.com")
            ->setNameContact("Contact")
            ->setEmailContact("contact@gmail.com")
            ->setContractNumber("#1")
            ->setContractDateStart(new DateTime)
            ->setContractDateEnd(new DateTime)
        ;
        $manager->persist($supplier_0);

        // ---

        $supplier_0 = (new Supplier)
            ->setName("Sup 2")
            ->setNameLegal("Legal Sup 2")
            ->setDescription("Description")
            ->setLogoName("some_logo.jpg")
            ->setPhoneNumberSupplier("+38 (011) 111-11-11")
            ->setEmailSupplier("supplier@gmail.com")
            ->setNameContact("Contact")
            ->setEmailContact("contact@gmail.com")
            ->setContractNumber("#1")
            ->setContractDateStart(new DateTime)
            ->setContractDateEnd(new DateTime)
        ;
        $manager->persist($supplier_0);

        // ---

        $manager->flush();
    }

    public function getOrder()
    {
        return 8;
    }
}