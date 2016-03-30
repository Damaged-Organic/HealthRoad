<?php
// src/AppBundle/Controller/Binding/PurchaseServiceController.php
namespace AppBundle\Controller\Binding;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException,
    Symfony\Bundle\FrameworkBundle\Controller\Controller;

use JMS\DiExtraBundle\Annotation as DI;

use AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Controller\Utility\Traits\ClassOperationsTrait,
    AppBundle\Entity\Student\Student,
    AppBundle\Service\Security\PurchaseServiceBoundlessAccess;

class PurchaseServiceController extends Controller implements UserRoleListInterface
{
    use ClassOperationsTrait;

    /** @DI\Inject("doctrine.orm.entity_manager") */
    private $_manager;

    /** @DI\Inject("app.security.purchase_service_boundless_access") */
    private $_purchaseServiceBoundlessAccess;

    public function showAction($objectClass, $objectId)
    {
        if( !$this->_purchaseServiceBoundlessAccess->isGranted(PurchaseServiceBoundlessAccess::PURCHASE_SERVICE_READ) )
            throw $this->createAccessDeniedException('Access denied');

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Student, $objectClass):
                $object = $this->_manager->getRepository('AppBundle:Student\Student')->find($objectId);

                if( !$object )
                    throw $this->createNotFoundException("Student identified by `id` {$objectId} not found");

                $purchasesService = $object->getPurchasesService();
            break;

            default:
                throw new NotAcceptableHttpException("Object not supported");
            break;
        }

        return $this->render('AppBundle:Entity/PurchaseService/Binding:show.html.twig', [
            'standalone'       => TRUE,
            'purchasesService' => $purchasesService,
            'object'           => $object
        ]);
    }
}
