<?php
// src/AppBundle/Controller/Payment/PaymentReceiptController.php
namespace AppBundle\Controller\Payment;

use DateTime;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\RedirectResponse,
    Symfony\Bundle\FrameworkBundle\Controller\Controller;

use JMS\DiExtraBundle\Annotation as DI;

use AppBundle\Controller\Utility\Traits\FormErrorsTrait,
    AppBundle\Service\Payment\Utility\PaymentReceiptFileInterface,
    AppBundle\Entity\Payment\PaymentReceipt,
    AppBundle\Model\Payment\PaymentReceiptFile,
    AppBundle\Form\Type\Payment\PaymentReceiptType,
    AppBundle\Service\Security\PaymentReceiptBoundlessAccess;

class PaymentReceiptController extends Controller implements PaymentReceiptFileInterface
{
    use FormErrorsTrait;

    /** @DI\Inject("doctrine.orm.entity_manager") */
    private $_manager;

    /** @DI\Inject("translator") */
    private $_translator;

    /** @DI\Inject("app.common.breadcrumbs") */
    private $_breadcrumbs;

    /** @DI\Inject("app.common.messages") */
    private $_messages;

    /** @DI\Inject("app.payment.receipt.parser") */
    private $_paymentReceiptParser;

    /** @DI\Inject("app.payment.receipt.validator") */
    private $_paymentReceiptValidator;

    /** @DI\Inject("app.payment.receipt.manager") */
    private $_paymentReceiptManager;

    /** @DI\Inject("app.payment.receipt.storage") */
    private $_paymentReceiptStorage;

    /** @DI\Inject("app.security.payment_receipt_boundless_access") */
    private $_paymentReceiptBoundlessAccess;

    public function paymentReceiptReplenishFormAction()
    {
        $form = $this->createForm(new PaymentReceiptType, new PaymentReceiptFile, [
            'action' => $this->generateUrl('payment_receipt_replenish_submit')
        ]);

        return $this->render('AppBundle:Entity/PaymentReceipt/Form:paymentReceiptReplenish.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Method({"POST"})
     * @Route(
     *      "/payment_receipt/replenish/submit",
     *      name="payment_receipt_replenish_submit",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"}
     * )
     */
     public function paymentReceiptReplenishSubmitAction(Request $request)
     {
         if( !$this->_paymentReceiptBoundlessAccess->isGranted(PaymentReceiptBoundlessAccess::PAYMENT_RECEIPT_REPLENISH) )
             throw $this->createAccessDeniedException('Access denied');

         $form = $this->createForm(new PaymentReceiptType, ($paymentReceiptFile = new PaymentReceiptFile));

         $form->handleRequest($request);

         if( !$form->isValid() ) {
             $this->_messages->markPaymentReceiptCheckErrors($this->getFormErrorMessages($form));
         } else {
             $receiptFile = $paymentReceiptFile->getPaymentReceiptFile();

             if( !($receipt = $this->_paymentReceiptParser->parseReceiptFile($receiptFile)) ) {
                 $this->_messages->markPaymentReceiptCheckErrors(['parsing problem']);
             } else {
                 $receipt = $this->_paymentReceiptParser->standardizeReceipt($receipt);

                 $receipt = $this->_paymentReceiptValidator->validateAndMarkReceiptFields($receipt);
                 $receipt = $this->_paymentReceiptValidator->validateAndMarkReceiptExistence($receipt);

                 $receipt = $this->_paymentReceiptManager->calculateAndSetProfits($receipt);

                 $this->_paymentReceiptStorage->saveReceipt($receipt);

                 $this->_messages->markPaymentReceiptCheckSuccess();

                 return $this->redirectToRoute('payment_receipt_replenish_check');
             }
         }

         return new RedirectResponse($request->headers->get('referer'));
     }

     /**
      * @Method({"GET"})
      * @Route(
      *      "/payment_receipt/replenish/check",
      *      name="payment_receipt_replenish_check",
      *      host="{domain_dashboard}",
      *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
      *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"}
      * )
      */
     public function paymentReceiptReplenishCheckAction()
     {
         if( !$this->_paymentReceiptBoundlessAccess->isGranted(PaymentReceiptBoundlessAccess::PAYMENT_RECEIPT_REPLENISH) )
             throw $this->createAccessDeniedException('Access denied');

         $receipt = $this->_paymentReceiptStorage->readReceipt();

         if( !$receipt )
            return $this->redirectToRoute('payment_receipt_read');

         $statusOrder = [self::RECEIPT_VALID, self::RECEIPT_EXISTS, self::RECEIPT_UNBINDED, self::RECEIPT_MISMATCH, self::RECEIPT_INVALID];
         usort($receipt, function($a, $b) use($statusOrder) {
             $position_a = array_search($a[self::RECEIPT_FIELD_STATUS], $statusOrder);
             $position_b = array_search($b[self::RECEIPT_FIELD_STATUS], $statusOrder);

             return $position_a - $position_b;
         });

         $paymentReceiptsTotal = count($receipt);
         $paymentReceiptsValid = array_reduce($receipt, function($carry, $item) {
             if( $item[self::RECEIPT_FIELD_STATUS] === self::RECEIPT_VALID )
                 $carry++;
             return $carry;
         }, 0);

         $paymentReceipts = [];

         foreach( $receipt as $entry )
             $paymentReceipts[] = (new PaymentReceipt)->constructFromPaymentReceiptFileEntry($entry);

         $this->_breadcrumbs->add('payment_receipt_read')->add('payment_receipt_replenish_check');

         return $this->render('AppBundle:Entity/PaymentReceipt/Replenish:replenishList.html.twig', [
             'paymentReceipts'       => $paymentReceipts,
             'paymentReceiptsTotal'  => $paymentReceiptsTotal,
             'paymentReceiptsValid'  => $paymentReceiptsValid
         ]);
     }

     /**
      * @Method({"GET"})
      * @Route(
      *      "/payment_receipt/replenish/persist",
      *      name="payment_receipt_replenish_persist",
      *      host="{domain_dashboard}",
      *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
      *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"}
      * )
      */
     public function paymentReceiptReplenishPersistAction()
     {
        if( !$this->_paymentReceiptBoundlessAccess->isGranted(PaymentReceiptBoundlessAccess::PAYMENT_RECEIPT_REPLENISH) )
            throw $this->createAccessDeniedException('Access denied');

        $receipt = $this->_paymentReceiptStorage->readReceipt();

        $paymentReceipts = [];

        foreach( $receipt as $entry )
        {
            if( $entry[self::RECEIPT_FIELD_STATUS] === self::RECEIPT_VALID )
                $paymentReceipts[] = (new PaymentReceipt)->constructFromPaymentReceiptFileEntry($entry);
        }

        if( $paymentReceipts )
        {
            $paymentReceipts = $this->_paymentReceiptManager->findAndSetRelatedEntities($paymentReceipts);

            // One-pass transaction to persist PaymentReceipt and Student (with replenished balance)
            $this->_manager->transactional(function($_manager) use($paymentReceipts) {
                $this->_manager->getRepository('AppBundle:Payment\PaymentReceipt')->rawInsertPaymentReceipts($paymentReceipts);
                $this->_paymentReceiptManager->replenishStudentsTotalLimit($paymentReceipts);
            });

            $this->_messages->markPaymentReceiptReplenishSuccess();
        } else {
            $this->_messages->markPaymentReceiptReplenishErrors([
                [$this->_translator->trans('payment.receipt.replenish.error', [], 'responses')]
            ]);
        }

        return $this->redirectToRoute('payment_receipt_replenish_clear');
     }

     /**
      * @Method({"GET"})
      * @Route(
      *      "/payment_receipt/replenish/clear",
      *      name="payment_receipt_replenish_clear",
      *      host="{domain_dashboard}",
      *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
      *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"}
      * )
      */
     public function paymentReceiptReplenishClearAction()
     {
         if( !$this->_paymentReceiptBoundlessAccess->isGranted(PaymentReceiptBoundlessAccess::PAYMENT_RECEIPT_REPLENISH) )
             throw $this->createAccessDeniedException('Access denied');

         $receipt = $this->_paymentReceiptStorage->clearReceipt();

         return $this->redirectToRoute('payment_receipt_read');
     }
}
