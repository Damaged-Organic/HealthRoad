<?php
// AppBundle/DataFixtures/ORM/LoadWebsiteMetadata.php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture,
    Doctrine\Common\DataFixtures\OrderedFixtureInterface,
    Doctrine\Common\Persistence\ObjectManager;

use AppBundle\Entity\Website\Metadata\Metadata;

class LoadWebsiteMetadata extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $manager->persist
        (
            $websiteMetadata_1 = (new Metadata)
                ->setRoute("website_index")
                ->setTitle("Головна")
                ->setDescription("")
                ->setRobots("index, follow")
        );

        $manager->persist
        (
            $websiteMetadata_2 = (new Metadata)
                ->setRoute("website_our_project")
                ->setTitle("Наш проект")
                ->setDescription("")
                ->setRobots("index, follow")
        );

        $manager->persist(
            $websiteMetadata_2_1 = (new Metadata)
                ->setRoute("website_how_to_get_card")
                ->setTitle("Як отримати картку?")
                ->setDescription("")
                ->setRobots("index, follow")
        );

        $manager->persist(
            $websiteMetadata_2_2 = (new Metadata)
                ->setRoute("website_how_to_replenish_card")
                ->setTitle("Як поповнити картку?")
                ->setDescription("")
                ->setRobots("index, follow")
        );

        $manager->persist(
            $websiteMetadata_2_3 = (new Metadata)
                ->setRoute("website_how_to_use_vending_machine")
                ->setTitle("Як користуватись автоматом?")
                ->setDescription("")
                ->setRobots("index, follow")
        );

        $manager->persist(
            $websiteMetadata_3 = (new Metadata)
                ->setRoute("website_about_company")
                ->setTitle("Про компанію")
                ->setDescription("")
                ->setRobots("index, follow")
        );

        $manager->persist(
            $websiteMetadata_3_1 = (new Metadata)
                ->setRoute("website_news")
                ->setTitle("Новини")
                ->setDescription("")
                ->setRobots("index, follow")
        );

        $manager->persist(
            $websiteMetadata_3_2 = (new Metadata)
                ->setRoute("website_promotions")
                ->setTitle("Акції")
                ->setDescription("")
                ->setRobots("index, follow")
        );

        $manager->persist(
            $websiteMetadata_3_3 = (new Metadata)
                ->setRoute("website_gallery")
                ->setTitle("Галерея")
                ->setDescription("")
                ->setRobots("index, follow")
        );

        $manager->persist(
            $websiteMetadata_4 = (new Metadata)
                ->setRoute("website_our_partners")
                ->setTitle("Наші партнери")
                ->setDescription("")
                ->setRobots("index, follow")
        );

        $manager->persist(
            $websiteMetadata_5 = (new Metadata)
                ->setRoute("website_products")
                ->setTitle("Продукти")
                ->setDescription("")
                ->setRobots("index, follow")
        );

        $manager->persist(
            $websiteMetadata_6 = (new Metadata)
                ->setRoute("website_contacts")
                ->setTitle("Контакти")
                ->setDescription("")
                ->setRobots("index, follow")
        );

        $manager->persist(
            $websiteMetadata_6_1 = (new Metadata)
                ->setRoute("website_vending_machines_placement")
                ->setTitle("Розташування автоматів")
                ->setDescription("")
                ->setRobots("index, follow")
        );

        $manager->persist(
            $websiteMetadata_6_2 = (new Metadata)
                ->setRoute("website_vending_machines_suppliers")
                ->setTitle("Для постачальників")
                ->setDescription("")
                ->setRobots("index, follow")
        );

        $manager->persist(
            $websiteMetadata_7 = (new Metadata)
                ->setRoute("website_feedback")
                ->setTitle("Зворотній зв'язок")
                ->setDescription("")
                ->setRobots("index, follow")
        );

        $manager->flush();
    }

    public function getOrder()
    {
        return 100;
    }
}