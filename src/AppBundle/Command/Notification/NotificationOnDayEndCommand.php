<?php
// src/AppBundle/Command/Notification/NotificationOnDayEndCommand.php
namespace AppBundle\Command\Notification;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface;

class NotificationOnDayEndCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('dz:notification:on_day_end')
            ->setDescription('Send Notification at the end of day')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $_container = $this->getContainer();

        $_notificationManager = $_container->get('app.notification.manager');

        $_notificationManager->processNotificationsOnDayEnd();
    }
}
