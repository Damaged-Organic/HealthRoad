<?php
// AppBundle/EventListener/Fallback/IeFallbackListener.php
namespace AppBundle\EventListener\Fallback;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class IeFallbackListener
{
    private $_ieFallbackController;

    public function setIeFallbackController($ieFallbackController)
    {
        $this->_ieFallbackController = $ieFallbackController;
    }

    public function onKernelRequest($event)
    {
        if( isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/(?i)msie [5-8]/', $_SERVER['HTTP_USER_AGENT']) )
            $event->setResponse($this->_ieFallbackController->ieFallbackAction());
    }
}
