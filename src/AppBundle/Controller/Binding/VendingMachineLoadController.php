<?php
// AppBundle/Controller/Binding/VendingMachineLoadController.php
namespace AppBundle\Controller\Binding;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException,
    Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Controller\Utility\Traits\ClassOperationsTrait,
    AppBundle\Entity\VendingMachine\VendingMachine,
    AppBundle\Service\Security\VendingMachineLoadBoundlessAccess;

class VendingMachineLoadController extends Controller implements UserRoleListInterface
{
    use ClassOperationsTrait;

    public function showAction($objectClass, $objectId)
    {
        $_vendingMachineLoadBoundlessAccess = $this->get('app.security.vending_machine_load_boundless_access');

        if( !$_vendingMachineLoadBoundlessAccess->isGranted(VendingMachineLoadBoundlessAccess::VENDING_MACHINE_LOAD_READ) )
            throw $this->createAccessDeniedException('Access denied');

        $_manager = $this->getDoctrine()->getManager();

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new VendingMachine, $objectClass):
                $object = $_manager->getRepository('AppBundle:VendingMachine\VendingMachine')->find($objectId);

                if( !$object )
                    throw $this->createNotFoundException("Vending Machine identified by `id` {$objectId} not found");

                $vendingMachineLoads = $object->getVendingMachineLoad();
            break;

            default:
                throw new NotAcceptableHttpException("Object not supported");
            break;
        }

        return $this->render('AppBundle:Entity/VendingMachineLoad/Binding:show.html.twig', [
            'standalone'          => TRUE,
            'vendingMachineLoads' => $vendingMachineLoads,
            'object'              => $object
        ]);
    }
}