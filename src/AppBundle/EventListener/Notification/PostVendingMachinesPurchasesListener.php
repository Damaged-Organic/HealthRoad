<?php
// src/AppBundle/EventListener/Notification/PostVendingMachinesPurchasesListener.php
namespace AppBundle\EventListener\Notification;

use Doctrine\ORM\EntityManager;

use AppBundle\Event\PostVendingMachinesPurchasesEvent,
    AppBundle\Service\Notification\NotificationManager,
    AppBundle\Service\Notification\NotificationSender;

class PostVendingMachinesPurchasesListener
{
    private $_notificationManager;
    private $_notificationSender;

    public function setNotificationManager(NotificationManager $notificationManager)
    {
        $this->_notificationManager = $notificationManager;
    }

    public function setNotificationSender(NotificationSender $notificationSender)
    {
        $this->_notificationSender = $notificationSender;
    }

    public function processNotifications(PostVendingMachinesPurchasesEvent $event)
    {
        $this->_notificationManager->processNotificationsOnSync(
            $event->getVendingMachine(),
            $event->getVendingMachineSyncId()
        );
    }
}
