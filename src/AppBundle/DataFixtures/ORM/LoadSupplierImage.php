<?php
// AppBundle/DataFixtures/ORM/LoadSupplierImage.php
namespace AppBundle\DataFixtures\ORM;

use DateTime;

use Doctrine\Common\DataFixtures\AbstractFixture,
    Doctrine\Common\DataFixtures\OrderedFixtureInterface,
    Doctrine\Common\Persistence\ObjectManager;

use AppBundle\Entity\Supplier\SupplierImage;

class LoadSupplierImage extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $manager->persist
        (
            $supplierImage_1 = (new SupplierImage)
                ->setSupplier($this->getReference("supplier_vitmark"))
                ->setImageSupplierName("nash-sik.png")
                ->setUpdatedAt(new DateTime)
        );

        $manager->persist
        (
            $supplierImage_2 = (new SupplierImage)
                ->setSupplier($this->getReference("supplier_vitmark"))
                ->setImageSupplierName("jaffa.png")
                ->setUpdatedAt(new DateTime)
        );

        // ---

        $manager->persist
        (
            $supplierImage_3 = (new SupplierImage)
                ->setSupplier($this->getReference("supplier_ekonia"))
                ->setImageSupplierName("teen-team.png")
                ->setUpdatedAt(new DateTime)
        );

        // ---

        $manager->persist
        (
            $supplierImage_4 = (new SupplierImage)
                ->setSupplier($this->getReference("supplier_sergio"))
                ->setImageSupplierName("frucfetta.png")
                ->setUpdatedAt(new DateTime)
        );

        // ---

        $manager->persist
        (
            $supplierImage_5 = (new SupplierImage)
                ->setSupplier($this->getReference("supplier_kvartet"))
                ->setImageSupplierName("ginger.png")
                ->setUpdatedAt(new DateTime)
        );

        // ---

        $manager->flush();
    }

    public function getOrder()
    {
        return 9;
    }
}