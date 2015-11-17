<?php
// AppBundle/DataFixtures/ORM/LoadProductCategory.php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture,
    Doctrine\Common\DataFixtures\OrderedFixtureInterface,
    Doctrine\Common\Persistence\ObjectManager;

use AppBundle\Entity\Product\ProductCategory;

class LoadProductCategory extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $productCategory_0 = (new ProductCategory)
            ->setName("Вода")
        ;
        $manager->persist($productCategory_0);

        $productCategory_1 = (new ProductCategory)
            ->setName("Кондитерські вироби")
        ;
        $manager->persist($productCategory_1);

        $productCategory_2 = (new ProductCategory)
            ->setName("Соки")
        ;
        $manager->persist($productCategory_2);

        $productCategory_3 = (new ProductCategory)
            ->setName("Фрукти")
        ;
        $manager->persist($productCategory_3);

        $manager->flush();
    }

    public function getOrder()
    {
        return 8;
    }
}