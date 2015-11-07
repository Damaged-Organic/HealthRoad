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

    public function sendReportAccounting($filePath = NULL)
    {
        if( !$this->emailParameters['no_reply'] )
            return FALSE;

        $accountingEmailSetting = $this->_manager->getRepository('AppBundle:Setting\Setting')->findAccountingEmail();

        if( !$accountingEmailSetting )
            return FALSE;

        $email = [
            'from' => [$this->emailParameters['no_reply'] => "Автоматическая рассылка отчетов системы \"Дорога Здоровья\""],
            'to'   => $accountingEmailSetting->getSettingValue()
        ];

        $attachment = NULL;

        if( $filePath )
        {
            $email = $email + [
                'subject' => "Отчет для бухгалтеров (" . (new DateTime)->modify('yesterday')->format('m/d/Y') . ")",
                'body'    => "Отчет по продажам сети за торговый день."
            ];

            $attachment = \Swift_Attachment::fromPath($filePath, "application/vnd.ms-excel");
        } else {
            $email = $email + [
                'subject' => "[Отчет пуст] Отчет для бухгалтеров (" . (new DateTime)->modify('yesterday')->format('m/d/Y') . ")",
                'body'    => "В системе не было данных, по которым можно было бы составить отчет по продажам сети за торговый день."
            ];
        }

        return $this->_mailerShortcut->sendMail(
            $email['from'], $email['to'], $email['subject'], $email['body'], $attachment
        );
    }

    public function sendReportLogistics($filePath = NULL)
    {
        if( !$this->emailParameters['no_reply'] )
            return FALSE;

        $logisticsEmailSetting = $this->_manager->getRepository('AppBundle:Setting\Setting')->findLogisticsEmail();

        if( !$logisticsEmailSetting )
            return FALSE;

        $email = [
            'from' => [$this->emailParameters['no_reply'] => "Автоматическая рассылка отчетов системы \"Дорога Здоровья\""],
            'to'   => $logisticsEmailSetting->getSettingValue()
        ];

        $attachment = NULL;

        if( !$filePath )
        {
            $email = $email + [
                'subject' => "Отчет для логистики (" . (new DateTime)->format('m/d/Y') . ")",
                'body' => "Отчет с картами продаж торговых автоматов."
            ];

            $attachment = \Swift_Attachment::fromPath($filePath, "application/vnd.ms-excel");
        } else {
            $email = $email + [
                'subject' => "[Отчет пуст] Отчет для логистики (" . (new DateTime)->format('m/d/Y') . ")",
                'body'    => "В системе не было данных, по которым можно было бы составить отчет с картами продаж торговых автоматов."
            ];
        }

        return $this->_mailerShortcut->sendMail(
            $email['from'], $email['to'], $email['subject'], $email['body'], $attachment
        );
    }
}