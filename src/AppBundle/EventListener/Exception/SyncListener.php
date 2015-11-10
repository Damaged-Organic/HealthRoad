<?php
// AppBundle/EventListener/Exception/SyncListener.php
namespace AppBundle\EventListener\Exception;

use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface,
    Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent,
    Symfony\Component\HttpFoundation\Response;

use Psr\Log\LoggerInterface;

class SyncListener
{
    const SYNC_MARKER_INTERFACE = 'AppBundle\Controller\Utility\Interfaces\Markers\SyncAuthenticationMarkerInterface';

    private $logger;

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if( strpos($event->getRequest()->attributes->get('_controller'), '::') )
        {
            $implementedInterfaces = class_implements(explode('::', $event->getRequest()->attributes->get('_controller'))[0]);

            if (in_array(self::SYNC_MARKER_INTERFACE, $implementedInterfaces, TRUE)) {
                $exception = $event->getException();

                $message = sprintf(
                    '`%s` [uncaught exception]: throws `%s` (code `%s`) at `%s` line `%s`',
                    get_class($exception),
                    $exception->getMessage(),
                    $exception->getCode(),
                    $exception->getFile(),
                    $exception->getLine()
                );

                $this->logger->error($message, ['exception' => $exception]);
            }
        }
    }
}