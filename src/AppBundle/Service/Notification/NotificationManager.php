<?php
// src/AppBundle/Service/Notification/NotificationManager.php
namespace AppBundle\Service\Notification;

use DateTime;

use Doctrine\ORM\EntityManager;

use AppBundle\Entity\VendingMachine\VendingMachine,
    AppBundle\Model\Notification\Notification,
    AppBundle\Service\Notification\NotificationBuilder,
    AppBundle\Service\Notification\NotificationSender,
    AppBundle\Service\PurchaseService\PurchaseServiceManager;

class NotificationManager
{
    private $_manager;

    private $_notificationBuilder;
    private $_notificationSender;

    private $_purchaseServiceManager;

    private $settingStudentWarningLimit;

    public function setManager(EntityManager $manager)
    {
        $this->_manager = $manager;
    }

    public function setNotificationBuilder(NotificationBuilder $notificationBuilder)
    {
        $this->_notificationBuilder = $notificationBuilder;
    }

    public function setNotificationSender(NotificationSender $notificationSender)
    {
        $this->_notificationSender = $notificationSender;
    }

    public function setPurchaseServiceManager(PurchaseServiceManager $purchaseServiceManager)
    {
        $this->_purchaseServiceManager = $purchaseServiceManager;
    }

    public function processNotificationsOnSync(VendingMachine $vendingMachine, $vendingMachineSyncId)
    {
        $this->settingStudentWarningLimit = $this->_manager->getRepository('AppBundle:Setting\Setting')
            ->findStudentWarningLimit();

        $this->_manager->clear();

        $settingSmsExchangeRate = $this->_manager->getRepository('AppBundle:Setting\Setting')
            ->findSmsExchangeRate();

        $customerPurchases = $this->getClientPurchasesOnSync($vendingMachine, $vendingMachineSyncId);

        $notifications = $this->prepareNotificationMessages($customerPurchases);

        list($smsList, $smsWarningList, $emailList) = $this->populateNotificationsOnSync($notifications);

        $chargedNotifications = $this->manageNotificationsSendOnSync($smsList, $smsWarningList, $emailList);

        if( $chargedNotifications )
        {
            $this->_purchaseServiceManager->purchaseNotificationMessages(
                $chargedNotifications,
                $settingSmsExchangeRate
            );
        }
    }

    public function processNotificationsOnDayEnd()
    {
        $this->settingStudentWarningLimit = $this->_manager->getRepository('AppBundle:Setting\Setting')
            ->findStudentWarningLimit();

        $this->_manager->clear();

        $settingSmsExchangeRate = $this->_manager->getRepository('AppBundle:Setting\Setting')
            ->findSmsExchangeRate();

        $customerPurchases = $this->getClientPurchasesOnDayEnd();

        $notifications = $this->prepareNotificationMessages($customerPurchases);

        list($smsList, $emailList) = $this->populateNotificationsOnDayEnd($notifications);

        $chargedNotifications = $this->manageNotificationsSendOnDayEnd($smsList, $emailList);

        if( $chargedNotifications )
        {
            $this->_purchaseServiceManager->purchaseNotificationMessages(
                $chargedNotifications,
                $settingSmsExchangeRate
            );
        }
    }

    private function getClientPurchasesOnSync(VendingMachine $vendingMachine, $vendingMachineSyncId)
    {
        return $this->_manager->getRepository('AppBundle:Customer\Customer')->findPurchasesOnSync(
            $vendingMachine,
            $vendingMachineSyncId
        );
    }

    private function getClientPurchasesOnDayEnd()
    {
        return $this->_manager->getRepository('AppBundle:Customer\Customer')->findPurchasesOnDayEnd();
    }

    private function prepareNotificationMessages($customerPurchases)
    {
        $notifications = [];

        foreach($customerPurchases as $customer)
        {
            if( !$customer->getStudents() )
                continue;

            foreach( $customer->getStudents() as $student )
            {
                $productsArray = [];

                if( $student->getPurchases() )
                {
                    foreach( $student->getPurchases() as $purchase )
                    {
                        if( $purchase->getProduct() )
                            $productsArray[] = $purchase->getProduct();
                    }
                }

                if( $productsArray )
                {
                    $notification = new Notification;

                    $notification
                        ->setCustomer($customer)
                        ->setCustomerNotificationSetting(
                            $customer->getCustomerNotificationSetting()
                        )
                        ->setStudent($student)
                        ->setPurchasedAt(new DateTime)
                        ->setProductsArray($productsArray)
                    ;

                    $notifications[] = $notification;
                }
            }
        }

        return $notifications;
    }

    public function populateNotificationsOnSync(array $notifications)
    {
        $smsList = $smsWarningList = $emailList = [];

        foreach( $notifications as $notification )
        {
            if( !($notification instanceof Notification) )
                continue;

            $customerNotificationSetting = $notification->getCustomerNotificationSetting();

            $validForSms = $this->_purchaseServiceManager->validateStudentBalanceForSms(
                $notification->getStudent(),
                $this->settingStudentWarningLimit
            );

            // Check if Customer is able to send an SMS from it's Student balance.
            // If true, check if corresponding notification setting is enabled and
            // act accordingly, if false - send a warning SMS.
            if( $validForSms ) {
                if( $customerNotificationSetting->getSmsOnSync() )
                    $smsList[] = $notification;
            } else {
                $smsWarningList[] = $notification;
            }

            if( $customerNotificationSetting->getEmailOnSync() )
            {
                if( $notification->getCustomer()->getEmail() )
                    $emailList[] = $notification;
            }
        }

        return [$smsList, $smsWarningList, $emailList];
    }

    public function populateNotificationsOnDayEnd(array $notifications)
    {
        $smsList = $emailList = [];

        foreach( $notifications as $notification )
        {
            if( !($notification instanceof Notification) )
                continue;

            $customerNotificationSetting = $notification->getCustomerNotificationSetting();

            $validForSms = $this->_purchaseServiceManager->validateStudentBalanceForSms(
                $notification->getStudent(),
                $this->settingStudentWarningLimit
            );

            // Check if Customer is able to send an SMS from it's Student balance.
            // If true, check if corresponding notification setting is enabled and
            // act accordingly, if false - do nothing, because Customer is probably
            // already informed on sync or totally inactive.
            if( $validForSms ) {
                if( $customerNotificationSetting->getSmsOnDayEnd() )
                    $smsList[] = $notification;
            }

            if( $customerNotificationSetting->getEmailOnDayEnd() )
            {
                if( $notification->getCustomer()->getEmail() )
                    $emailList[] = $notification;
            }
        }

        return [$smsList, $emailList];
    }

    public function manageNotificationsSendOnSync(array $smsList, array $smsWarningList, array $emailList)
    {
        $chargedNotifications = [];

        if( $smsList )
        {
            $smsMessageList = $this->_notificationBuilder->buildNotificationsByTemplateOnSyncSms($smsList);

            if( $smsMessageList )
            {
                $chargedNotifications = array_merge(
                    $chargedNotifications,
                    $this->_notificationSender->sendSmsMessageListCharged($smsMessageList)
                );
            }
        }

        if( $smsWarningList )
        {
            $smsWarningMessageList = $this->_notificationBuilder->buildNotificationsByTemplateWarningSms(
                $smsWarningList,
                $this->settingStudentWarningLimit
            );

            if( $smsWarningMessageList )
                $this->_notificationSender->sendSmsMessageListFree($smsWarningMessageList);
        }

        if( $emailList )
        {
            $emailMessageList = $this->_notificationBuilder->buildNotificationsByTemplateOnSyncEmail($emailList);

            if( $emailMessageList )
                $this->_notificationSender->sendEmailMessageList($emailMessageList);
        }

        return $chargedNotifications;
    }

    public function manageNotificationsSendOnDayEnd(array $smsList, array $emailList)
    {
        $chargedNotifications = [];

        if( $smsList )
        {
            $smsMessageList = $this->_notificationBuilder->buildNotificationsByTemplateOnDayEndSms($smsList);

            if( $smsMessageList )
            {
                $chargedNotifications = array_merge(
                    $chargedNotifications,
                    $this->_notificationSender->sendSmsMessageListCharged($smsMessageList)
                );
            }
        }

        if( $emailList )
        {
            $emailMessageList = $this->_notificationBuilder->buildNotificationsByTemplateOnDayEndEmail($emailList);

            if( $emailMessageList )
                $this->_notificationSender->sendEmailMessageList($emailMessageList);
        }

        return $chargedNotifications;
    }
}
