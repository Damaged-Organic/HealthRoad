<?php
// AppBundle/Controller/Binding/PurchaseController.php
namespace AppBundle\Controller\Binding;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException,
    Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Controller\Utility\Traits\ClassOperationsTrait,
    AppBundle\Entity\VendingMachine\VendingMachine,
    AppBundle\Service\Security\PurchaseBoundlessAccess;

class PurchaseController extends Controller implements UserRoleListInterface
{
    use ClassOperationsTrait;

    public function showAction($objectClass, $objectId)
    {
        $_purchaseBoundlessAccess = $this->get('app.security.purchase_boundless_access');

        if( !$_purchaseBoundlessAccess->isGranted(PurchaseBoundlessAccess::PURCHASE_READ) )
            throw $this->createAccessDeniedException('Access denied');

        $_manager = $this->getDoctrine()->getManager();

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new VendingMachine, $objectClass):
                $object = $_manager->getRepository('AppBundle:VendingMachine\VendingMachine')->find($objectId);

                if( !$object )
                    throw $this->createNotFoundException("Employee identified by `id` {$objectId} not found");

                $purchases = $object->getPurchases();
            break;

            default:
                throw new NotAcceptableHttpException("Object not supported");
            break;
        }

        return $this->render('AppBundle:Entity/Purchase/Binding:show.html.twig', [
            'standalone' => TRUE,
            'purchases'  => $purchases,
            'object'     => $object
        ]);
    }
}