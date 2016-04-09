<?php
// src/AppBundle/EventListener/Exception/NotificationListener.php
namespace AppBundle\EventListener\Exception;

use Symfony\Component\Console\Event\ConsoleExceptionEvent;

use Psr\Log\LoggerInterface;

class NotificationListener
{
    private $logger;

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function onConsoleException(ConsoleExceptionEvent $event)
    {
        $command = $event->getCommand();

        if( in_array($command->getName(), ['dz:notification:on_day_end'], TRUE) )
        {
            $exception = $event->getException();

            $message = sprintf(
                '`%s` [uncaught exception]: command `%s` throws `%s` at `%s` line `%s`',
                get_class($exception),
                $command->getName(),
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine()
            );

            $this->logger->error($message, ['exception' => $exception]);
        }
    }
}
