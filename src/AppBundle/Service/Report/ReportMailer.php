<?php
// AppBundle/Service/Report/ReportMailer.php
namespace AppBundle\Service\Report;

use DateTime;

use Swift_Attachment;

use Doctrine\ORM\EntityManager;

use AppBundle\Service\Common\MailerShortcut;

class ReportMailer
{
    private $_manager;
    private $_mailerShortcut;

    private $emailParameters;

    public function setManager(EntityManager $manager)
    {
        $this->_manager = $manager;
    }

    public function setMailerShortcut(MailerShortcut $mailerShortcut)
    {
        $this->_mailerShortcut = $mailerShortcut;
    }

    public function setEmailParameters($emailParameters)
    {
        $this->emailParameters = $emailParameters;
    }

    public function sendReportAccounting($filePath)
    {
        if( !$this->emailParameters['no_reply'] )
            return FALSE;

        $from = [$this->emailParameters['no_reply'] => "Автоматическая рассылка отчетов системы \"Дорога Здоровья\""];

        $accountingEmailSetting = $this->_manager->getRepository('AppBundle:Setting\Setting')->findAccountingEmail();

        if( !$accountingEmailSetting )
            return FALSE;

        $to = $accountingEmailSetting->getSettingValue();

        $subject = "Отчет для бухгалтеров (" . (new DateTime)->modify('yesterday')->format('m/d/Y') . ")";

        $body = "Отчет по продажам сети за торговый день.";

        $attachment = \Swift_Attachment::fromPath($filePath, "application/vnd.ms-excel");

        return ( $this->_mailerShortcut->sendMail($from, $to, $subject, $body, $attachment) )
            ? TRUE
            : FALSE;
    }
}