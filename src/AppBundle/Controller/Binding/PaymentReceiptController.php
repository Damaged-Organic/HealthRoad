<?php
// src/AppBundle/Controller/Binding/PaymentReceipt.php
namespace AppBundle\Controller\Binding;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException,
    Symfony\Bundle\FrameworkBundle\Controller\Controller;

use JMS\DiExtraBundle\Annotation as DI;

use AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Controller\Utility\Traits\ClassOperationsTrait,
    AppBundle\Service\Security\PaymentReceiptBoundlessAccess,
    AppBundle\Entity\Student\Student;

class PaymentReceiptController extends Controller implements UserRoleListInterface
{
    use ClassOperationsTrait;

    /** @DI\Inject("doctrine.orm.entity_manager") */
    private $_manager;

    /** @DI\Inject("app.security.payment_receipt_boundless_access") */
    private $_paymentReceiptBoundlessAccess;

    public function showAction($objectClass, $objectId)
    {
        if( !$this->_paymentReceiptBoundlessAccess->isGranted(PaymentReceiptBoundlessAccess::PAYMENT_RECEIPT_READ) )
            throw $this->createAccessDeniedException('Access denied');

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Student, $objectClass):
                $object = $this->_manager->getRepository('AppBundle:Student\Student')->find($objectId);

                if( !$object )
                    throw $this->createNotFoundException("Student identified by `id` {$objectId} not found");

                $paymentReceipts = $object->getPaymentsReceipts();
            break;

            default:
                throw new NotAcceptableHttpException("Object not supported");
            break;
        }

        return $this->render('AppBundle:Entity/PaymentReceipt/Binding:show.html.twig', [
            'standalone'      => TRUE,
            'paymentReceipts' => $paymentReceipts,
            'object'          => $object
        ]);
    }
}
