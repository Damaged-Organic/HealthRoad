<?php
// AppBundle/Controller/Binding/VendingMachineEventController.php
namespace AppBundle\Controller\Binding;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException,
    Symfony\Bundle\FrameworkBundle\Controller\Controller;

use JMS\DiExtraBundle\Annotation as DI;

use AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Controller\Utility\Traits\ClassOperationsTrait,
    AppBundle\Entity\VendingMachine\VendingMachine,
    AppBundle\Service\Security\VendingMachineEventBoundlessAccess;

class VendingMachineEventController extends Controller implements UserRoleListInterface
{
    use ClassOperationsTrait;

    /** @DI\Inject("doctrine.orm.entity_manager") */
    private $_manager;

    /** @DI\Inject("app.security.vending_machine_event_boundless_access") */
    private $_vendingMachineEventBoundlessAccess;

    public function showAction($objectClass, $objectId)
    {
        if( !$this->_vendingMachineEventBoundlessAccess->isGranted(VendingMachineEventBoundlessAccess::VENDING_MACHINE_EVENT_READ) )
            throw $this->createAccessDeniedException('Access denied');

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new VendingMachine, $objectClass):
                $object = $this->_manager->getRepository('AppBundle:VendingMachine\VendingMachine')->find($objectId);

                if( !$object )
                    throw $this->createNotFoundException("Vending Machine identified by `id` {$objectId} not found");

                $vendingMachineEvents = $object->getVendingMachineEvents();
            break;

            default:
                throw new NotAcceptableHttpException("Object not supported");
            break;
        }

        return $this->render('AppBundle:Entity/VendingMachineEvent/Binding:show.html.twig', [
            'standalone'           => TRUE,
            'vendingMachineEvents' => $vendingMachineEvents,
            'object'               => $object
        ]);
    }
}