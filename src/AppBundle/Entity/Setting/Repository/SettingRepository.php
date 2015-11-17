<?php
// AppBundle/Entity/Setting/Repository/SettingRepository.php
namespace AppBundle\Entity\Setting\Repository;

use AppBundle\Entity\Utility\Extended\ExtendedEntityRepository;

class SettingRepository extends ExtendedEntityRepository
{
    public function findOne()
    {
        $query = $this->createQueryBuilder('s')
            ->select('s')
            ->setMaxResults(1)
            ->orderBy('s.id', 'ASC')
            ->getQuery()
        ;

        return $query->getSingleResult();
    }

    public function findSettingBySettingKey($settingKey)
    {
        $query = $this->createQueryBuilder('s')
            ->select('s, ss, sd')
            ->leftJoin('s.settingsString', 'ss')
            ->leftJoin('s.settingsDecimal', 'sd')
            ->where('ss.settingKey = :settingKey')
            ->orWhere('sd.settingKey = :settingKey')
            ->setParameter('settingKey', $settingKey)
            ->setMaxResults(1)
            ->getQuery()
        ;

        return $query->getSingleResult();
    }

    public function findAccountingEmail()
    {
        return $this->findSettingBySettingKey('email_accounting')
            ->getSettingsString()[0];
    }

    public function findLogisticsEmail()
    {
        return $this->findSettingBySettingKey('email_logistics')
            ->getSettingsString()[0];
    }

    public function findStudentMinDailyLimit()
    {
        return $this->findSettingBySettingKey('student_daily_limit_lower')
            ->getSettingsDecimal()[0];
    }

    public function findVendingMachineReportSumAmount()
    {
        return $this->findSettingBySettingKey('vending_machine_report_sum_amount')
            ->getSettingsDecimal()[0];
    }
}