<?php
// src/AppBundle/EventListener/Sync/SyncAuthenticationListener.php
namespace AppBundle\EventListener\Sync;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException,
    Symfony\Component\HttpKernel\Exception\NotFoundHttpException,
    Symfony\Component\HttpKernel\Event\FilterControllerEvent;

use Doctrine\ORM\EntityManager;

use AppBundle\Service\Sync\Security\Authentication,
    AppBundle\Controller\Utility\Interfaces\Markers\SyncAuthenticationMarkerInterface;

class SyncAuthenticationListener
{
    private $_manager;
    private $_authentication;

    public function setManager(EntityManager $manager)
    {
        $this->_manager = $manager;
    }

    public function setAuthentication(Authentication $authentication)
    {
        $this->_authentication = $authentication;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $request    = $event->getRequest();
        $controller = $event->getController();

        if( !is_array($controller) )
            return;

        if ($controller[0] instanceof SyncAuthenticationMarkerInterface)
        {
            $serial = $request->attributes->get('_route_params')['serial'];

            $vendingMachine = $this->_manager->getRepository('AppBundle:VendingMachine\VendingMachine')->findOneBy([
                'serial' => $serial
            ]);

            if( !$vendingMachine )
                throw new NotFoundHttpException('Vending Machine not found');

            if( !$this->_authentication->authenticate($request, $vendingMachine) )
                throw new AccessDeniedHttpException('Authentication failed');
        }
    }
}