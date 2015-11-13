<?php
// AppBundle/DataFixtures/ORM/LoadWebsiteContact.php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture,
    Doctrine\Common\DataFixtures\OrderedFixtureInterface,
    Doctrine\Common\Persistence\ObjectManager;

use AppBundle\Entity\Website\Contact\Contact;

class LoadWebsiteContact extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $manager->persist
        (
            $websiteContact_1 = (new Contact)
                ->setAlias("support_center")
                ->setTitle("Центр підтримки")
                ->setPosition(NULL)
                ->setSchedule("з 9:00 до 18:00")
                ->setPhoneNumber("+38 (044) 235-38-42")
                ->setEmail("info@kdz.com.ua")
                ->setMail("01054, м. Київ-54, А/С 12")
        );

        $manager->persist
        (
            $websiteContact_2 = (new Contact)
                ->setAlias("support_center_mts")
                ->setTitle("МТС")
                ->setPosition("")
                ->setSchedule("")
                ->setPhoneNumber("+38 (066) 539-39-39")
                ->setEmail("")
                ->setMail("")
        );

        $manager->persist
        (
            $websiteContact_3 = (new Contact)
                ->setAlias("support_center_kyivstar")
                ->setTitle("Київстар")
                ->setPosition("")
                ->setSchedule("")
                ->setPhoneNumber("+38 (068) 539-39-39")
                ->setEmail("")
                ->setMail("")
        );

        $manager->persist
        (
            $websiteContact_4 = (new Contact)
                ->setAlias("support_center_life")
                ->setTitle("life:)")
                ->setPosition("")
                ->setSchedule("")
                ->setPhoneNumber("+38 (063) 539-39-39")
                ->setEmail("")
                ->setMail("")
        );

        $manager->persist
        (
            $websiteContact_5 = (new Contact)
                ->setAlias("contact_placement")
                ->setTitle("Сірко Вероніка Юріївна")
                ->setPosition("керівник")
                ->setSchedule("")
                ->setPhoneNumber("+38 (050) 413-71-94")
                ->setEmail("v.sirko@kdz.com.ua")
                ->setMail("")
        );

        $manager->persist
        (
            $websiteContact_6 = (new Contact)
                ->setAlias("contact_suppliers")
                ->setTitle("Кирилюк Наталія Олегівна")
                ->setPosition("начальник відділу логістики")
                ->setSchedule("")
                ->setPhoneNumber("+38 (050) 311-97-92")
                ->setEmail("n.kirilyuk@kdz.com.ua")
                ->setMail("")
        );

        $manager->flush();
    }

    public function getOrder()
    {
        return 101;
    }
}