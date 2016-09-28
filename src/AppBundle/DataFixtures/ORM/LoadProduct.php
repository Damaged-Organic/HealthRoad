<?php
// AppBundle/DataFixtures/ORM/LoadProduct.php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture,
    Doctrine\Common\DataFixtures\OrderedFixtureInterface,
    Doctrine\Common\Persistence\ObjectManager;

use AppBundle\Entity\Product\Product;

class LoadProduct extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $product_0 = (new Product)
            ->setNameFull("Product 1")
            ->setNameShort("Prod 1")
            ->setNameNotification("P 1")
            ->setCode("P-01")
            ->setPrice(9.99)
            ->setDescriptionShort("")
            ->setDescription("")
            ->setCalories(100)
            ->setShelfLife("10 days")
            ->setStorageTemperatureMin(15)
            ->setStorageTemperatureMax(20)
            ->setWeight(100)
            ->setMeasurementUnit("bottle")
            ->setAmountInBox(100)
            ->setDisplayOrder(1)
        ;
        $manager->persist($product_0);

        $product_1 = (new Product)
            ->setNameFull("Product 2")
            ->setNameShort("Prod 2")
            ->setNameNotification("P 1")
            ->setCode("P-02")
            ->setPrice(19.99)
            ->setDescriptionShort("")
            ->setDescription("")
            ->setCalories(200)
            ->setShelfLife("22 hours")
            ->setStorageTemperatureMin(10)
            ->setStorageTemperatureMax(15)
            ->setWeight(250)
            ->setMeasurementUnit("box")
            ->setAmountInBox(50)
            ->setDisplayOrder(2)
        ;
        $manager->persist($product_1);

        $manager->flush();
    }

    public function getOrder()
    {
        return 9;
    }
}
