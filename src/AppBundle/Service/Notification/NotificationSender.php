<?php
// src/AppBundle/Service/Notification/NotificationSender.php
namespace AppBundle\Service\Notification;

use DateTime;

use Symfony\Component\Translation\TranslatorInterface;

use AppBundle\Model\Notification\Notification,
    AppBundle\Service\Common\MailerShortcut,
    AppBundle\Service\Notification\UniSender\UniSenderApiWrapper;

class NotificationSender
{
    private $_translator;

    private $emailParameters;
    private $phoneParameters;

    private $_uniSenderApiWrapper;
    private $_mailerShortcut;

    public function setTranslator(TranslatorInterface $translator)
    {
        $this->_translator = $translator;
    }

    public function setPhoneParameters($phoneParameters)
    {
        $this->phoneParameters = $phoneParameters;
    }

    public function setEmailParameters($emailParameters)
    {
        $this->emailParameters = $emailParameters;
    }

    public function setUniSenderApiWrapper(UniSenderApiWrapper $uniSenderApiWrapper)
    {
        $this->_uniSenderApiWrapper = $uniSenderApiWrapper;
    }

    public function setMailerShortcut(MailerShortcut $mailerShortcut)
    {
        $this->_mailerShortcut = $mailerShortcut;
    }

    public function sendSmsMessageListCharged(array $smsMessageList)
    {
        if( !$this->phoneParameters['alpha_name'] )
            return;

        $chargedNotifications = [];

        foreach( $smsMessageList as $notification )
        {
            $answer = $this->sendSms($notification);

            if( $answer )
            {
                $answer = json_decode($answer);

                if( !empty($answer->result) )
                {
                    if( !empty($answer->result->price) )
                    {
                        $notification->setPrice($answer->result->price);

                        $chargedNotifications[] = $notification;
                    }
                }
            }
        }

        return $chargedNotifications;
    }

    public function sendSmsMessageListFree(array $smsMessageList)
    {
        if( !$this->phoneParameters['alpha_name'] )
            return;

        foreach( $smsMessageList as $notification )
        {
            $this->sendSms($notification);
        }
    }

    private function sendSms(Notification $notification)
    {
        if( !$notification->getSmsMessage() )
            return FALSE;

        $phone = str_replace(['(', ')', '-', ' '], '', $notification->getCustomer()->getPhoneNumber());

        $answer = $this->_uniSenderApiWrapper->sendSms([
            'phone'  => $phone,
            'sender' => $this->phoneParameters['alpha_name'],
            'text'   => $notification->getSmsMessage()
        ]);

        return $answer;
    }

    public function sendEmailMessageList(array $emailMessageList)
    {
        if( !$this->emailParameters['no_reply'] )
            return;

        $subject = $this->_translator->trans('email.subject', [
            '%datetime%' => (new DateTime)->format('d/m H:i'),
        ], 'notifications', 'ua');

        foreach( $emailMessageList as $notification )
        {
            $this->sendEmail($notification, $subject);
        }
    }

    public function sendEmail(Notification $notification, $subject)
    {
        if( !$notification->getEmailMessage() )
            return FALSE;

        if( !$this->_mailerShortcut->validateEmail($notification->getCustomer()->getEmail()) )
            return FALSE;

        $result = $this->_mailerShortcut->sendMail(
            [$this->emailParameters['no_reply'] => 'KDZ'],
            $notification->getCustomer()->getEmail(),
            $subject,
            $notification->getEmailMessage()
        );

        return $result;
    }
}
