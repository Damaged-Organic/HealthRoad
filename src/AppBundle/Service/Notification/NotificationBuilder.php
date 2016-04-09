<?php
// src/AppBundle/Service/Notification/NotificationBuilder.php
namespace AppBundle\Service\Notification;

use DateTime;

use Symfony\Component\Translation\TranslatorInterface;

use AppBundle\Entity\Setting\SettingDecimal;

class NotificationBuilder
{
    private $_translator;

    public function setTranslator(TranslatorInterface $translator)
    {
        $this->_translator = $translator;
    }

    public function buildNotificationsByTemplateOnSyncSms(array $smsList)
    {
        $smsMessageList = [];

        foreach($smsList as $notification)
        {
            $products = [];

            foreach( $notification->getProductsArray() as $product )
            {
                $products[] = $this->_translator->trans('sms.purchase', [
                    '%product%' => $product->getNameNotification(),
                    '%price%'   => $product->getPrice()
                ], 'notifications', 'ua');
            }

            $smsMessage = $this->_translator->trans('sms.on_sync', [
                '%time%'      => $notification->getPurchasedAt()->format('H:i'),
                '%student%'   => $notification->getStudent()->getName(),
                '%purchases%' => implode(', ', $products),
                '%balance%'   => $notification->getStudent()->getTotalLimit()
            ], 'notifications', 'ua');

            $notification->setSmsMessage($smsMessage);

            $smsMessageList[] = $notification;
        }

        return $smsMessageList;
    }

    public function buildNotificationsByTemplateOnSyncEmail(array $emailList)
    {
        $emailMessageList = [];

        foreach($emailList as $notification)
        {
            $products = [];

            foreach( $notification->getProductsArray() as $product )
            {
                $products[] = $this->_translator->trans('email.purchase', [
                    '%product%' => $product->getNameFull(),
                    '%price%'   => $product->getPrice()
                ], 'notifications', 'ua');
            }

            $emailMessage = $this->_translator->trans('email.on_sync', [
                '%time%'      => $notification->getPurchasedAt()->format('H:i'),
                '%student%'   => $notification->getStudent()->getName(),
                '%purchases%' => implode(', ', $products),
                '%balance%'   => $notification->getStudent()->getTotalLimit()
            ], 'notifications', 'ua');

            $notification->setEmailMessage($emailMessage);

            $emailMessageList[] = $notification;
        }

        return $emailMessageList;
    }

    public function buildNotificationsByTemplateOnDayEndSms(array $smsList)
    {
        $smsMessageList = [];

        foreach($smsList as $notification)
        {
            $products = [];

            foreach( $notification->getProductsArray() as $product )
            {
                $products[] = $this->_translator->trans('sms.purchase', [
                    '%product%' => $product->getNameNotification(),
                    '%price%'   => $product->getPrice()
                ], 'notifications', 'ua');
            }

            $smsMessage = $this->_translator->trans('sms.on_day_end', [
                '%date%'      => $notification->getPurchasedAt()->format('d/m'),
                '%student%'   => $notification->getStudent()->getName(),
                '%purchases%' => implode(', ', $products),
                '%balance%'   => $notification->getStudent()->getTotalLimit()
            ], 'notifications', 'ua');

            $notification->setSmsMessage($smsMessage);

            $smsMessageList[] = $notification;
        }

        return $smsMessageList;
    }

    public function buildNotificationsByTemplateOnDayEndEmail(array $emailList)
    {
        $emailMessageList = [];

        foreach($emailList as $notification)
        {
            $products = [];

            foreach( $notification->getProductsArray() as $product )
            {
                $products[] = $this->_translator->trans('email.purchase', [
                    '%product%' => $product->getNameFull(),
                    '%price%'   => $product->getPrice()
                ], 'notifications', 'ua');
            }

            $emailMessage = $this->_translator->trans('email.on_day_end', [
                '%date%'      => $notification->getPurchasedAt()->format('d/m'),
                '%student%'   => $notification->getStudent()->getName(),
                '%purchases%' => implode(', ', $products),
                '%balance%'   => $notification->getStudent()->getTotalLimit()
            ], 'notifications', 'ua');

            $notification->setEmailMessage($emailMessage);

            $emailMessageList[] = $notification;
        }

        return $emailMessageList;
    }

    public function buildNotificationsByTemplateWarningSms(array $smsWarningList, SettingDecimal $settingStudentWarningLimit)
    {
        $smsWarningMessageList = [];

        foreach($smsWarningList as $notification)
        {
            $smsMessage = $this->_translator->trans('sms.warning', [
                '%setting_student_warning_limit%' => $settingStudentWarningLimit->getSettingValue(),
                '%student%'                       => $notification->getStudent()->getName()
            ], 'notifications', 'ua');

            $notification->setSmsMessage($smsMessage);

            $smsWarningMessageList[] = $notification;
        }

        return $smsWarningMessageList;
    }
}
