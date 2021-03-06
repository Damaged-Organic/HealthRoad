<?php
// src/AppBundle/DataFixtures/ORM/LoadSetting.php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture,
    Doctrine\Common\DataFixtures\OrderedFixtureInterface,
    Doctrine\Common\Persistence\ObjectManager;

use AppBundle\Entity\Setting\Setting,
    AppBundle\Entity\Setting\SettingDecimal,
    AppBundle\Entity\Setting\SettingString;

class LoadSetting extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $setting = (new Setting);

        $manager->persist($setting);
        $manager->flush();

        $this->addReference('setting', $setting);

        // ---

        $settingNfcTagActivationFee = (new SettingDecimal)
            ->setSetting($this->getReference('setting'))
            ->setName("Стоимость активации NFC-метки")
            ->setSettingKey("nfc_tag_activation_fee")
            ->setSettingValue(19.00)
        ;
        $manager->persist($settingNfcTagActivationFee);

        $settingStudentLowerDailyLimit = (new SettingDecimal)
            ->setSetting($this->getReference('setting'))
            ->setName("Нижний дневной лимит для ученика")
            ->setSettingKey("student_daily_limit_lower")
            ->setSettingValue(10.00)
        ;
        $manager->persist($settingStudentLowerDailyLimit);

        $settingStudentWarningLimit = (new SettingDecimal)
            ->setSetting($this->getReference('setting'))
            ->setName("Лимит ученика для отправки предупреждения")
            ->setSettingKey("student_warning_limit")
            ->setSettingValue(10.00)
        ;
        $manager->persist($settingStudentWarningLimit);

        $settingSmsExchangeRate = (new SettingDecimal)
            ->setSetting($this->getReference('setting'))
            ->setName("Курс конвертации для отправки уведомлений")
            ->setSettingKey("sms_exchange_rate")
            ->setSettingValue(26.30)
        ;
        $manager->persist($settingSmsExchangeRate);

        $settingVendingMachineReportSumAmount = (new SettingDecimal)
            ->setSetting($this->getReference('setting'))
            ->setName("Сумма продаж трогового автомата для отправки отчета")
            ->setSettingKey("vending_machine_report_sum_amount")
            ->setSettingValue(0.00)
        ;
        $manager->persist($settingVendingMachineReportSumAmount);

        // ---

        $settingEmailForReports = (new SettingString)
            ->setSetting($this->getReference('setting'))
            ->setName("Электронный адрес для получения отчетов бухгалтерии")
            ->setSettingKey("email_accounting")
            ->setSettingValue("some-address@kdz.com.ua")
        ;
        $manager->persist($settingEmailForReports);

        $settingEmailForReports = (new SettingString)
            ->setSetting($this->getReference('setting'))
            ->setName("Электронный адрес для получения отчетов логистики")
            ->setSettingKey("email_logistics")
            ->setSettingValue("some-address@kdz.com.ua")
        ;
        $manager->persist($settingEmailForReports);

        // ---

        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }
}
