<?php
// AppBundle/DataFixtures/ORM/LoadWebsiteMenu.php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture,
    Doctrine\Common\DataFixtures\OrderedFixtureInterface,
    Doctrine\Common\Persistence\ObjectManager;

use AppBundle\Entity\WebsiteMenu\WebsiteMenu,
    AppBundle\Entity\WebsiteMenu\Utility\MenuBlockListInterface;

class LoadWebsiteMenu extends AbstractFixture implements OrderedFixtureInterface, MenuBlockListInterface
{
    public function load(ObjectManager $manager)
    {
        $manager->persist
        (
            $websiteMenu_1 = (new WebsiteMenu)
                ->setRoute("website_index")
                ->setBlock(self::BLOCK_MAIN)
                ->setTitleShort("Головна")
        );

        $manager->persist
        (
            $websiteMenu_2 = (new WebsiteMenu)
                ->setRoute("website_our_project")
                ->setBlock(self::BLOCK_MAIN)
                ->setTitleShort("Наш проект")
                ->setTitleFull("Наш проект")
        );

        $manager->persist(
            $websiteMenu_2_1 = (new WebsiteMenu)
                ->setRoute("website_how_to_get_card")
                ->setBlock(self::BLOCK_OUR_PROJECT)
                ->setTitleShort("Як отримати картку?")
                ->setTitleFull("Як отримати картку або браслет")
        );

        $manager->persist(
            $websiteMenu_2_2 = (new WebsiteMenu)
                ->setRoute("website_how_to_replenish_card")
                ->setBlock(self::BLOCK_OUR_PROJECT)
                ->setTitleShort("Як поповнити картку?")
                ->setTitleFull("Як поповнити картку або браслет")
        );

        $manager->persist(
            $websiteMenu_2_3 = (new WebsiteMenu)
                ->setRoute("website_how_to_use_vending_machine")
                ->setBlock(self::BLOCK_OUR_PROJECT)
                ->setTitleShort("Як користуватись автоматом?")
                ->setTitleFull("Як користуватися вендінговим автоматом")
        );

        $manager->persist(
            $websiteMenu_3 = (new WebsiteMenu)
                ->setRoute("website_about_company")
                ->setBlock(self::BLOCK_MAIN)
                ->setTitleShort("Про компанію")
                ->setTitleFull("Про компанію")
        );

        $manager->persist(
            $websiteMenu_4 = (new WebsiteMenu)
                ->setRoute("website_our_partners")
                ->setBlock(self::BLOCK_MAIN)
                ->setTitleShort("Наші партнери")
        );

        $manager->persist(
            $websiteMenu_5 = (new WebsiteMenu)
                ->setRoute("website_products")
                ->setBlock(self::BLOCK_MAIN)
                ->setTitleShort("Продукти")
        );

        $manager->persist(
            $websiteMenu_6 = (new WebsiteMenu)
                ->setRoute("website_contacts")
                ->setBlock(self::BLOCK_MAIN)
                ->setTitleShort("Контакти")
                ->setTitleFull("Контакти головного офісу")
        );

        $manager->persist(
            $websiteMenu_6_1 = (new WebsiteMenu)
                ->setRoute("website_vending_machines_placement")
                ->setBlock(self::BLOCK_CONTACTS)
                ->setTitleShort("Розташування автоматів")
                ->setTitleFull("Розташування автоматів у школі")
        );

        $manager->persist(
            $websiteMenu_6_2 = (new WebsiteMenu)
                ->setRoute("website_vending_machines_suppliers")
                ->setBlock(self::BLOCK_CONTACTS)
                ->setTitleShort("Для постачальників")
                ->setTitleFull("Для постачальників")
        );

        $manager->persist(
            $websiteMenu_7 = (new WebsiteMenu)
                ->setRoute("website_feedback")
                ->setBlock(self::BLOCK_FEEDBACK)
                ->setTitleShort("Зворотній зв'язок")
        );

        $manager->flush();
    }

    public function getOrder()
    {
        return 100;
    }
}