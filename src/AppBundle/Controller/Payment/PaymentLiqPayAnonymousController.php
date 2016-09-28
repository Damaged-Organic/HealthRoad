<?php
// src/AppBundle/Controller/Payment/PaymentLiqPayAnonymousController.php
namespace AppBundle\Controller\Payment;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\RedirectResponse,
    Symfony\Bundle\FrameworkBundle\Controller\Controller;

use JMS\DiExtraBundle\Annotation as DI;

use AppBundle\Validator\Constraints as CustomAssert,
    AppBundle\Controller\Utility\Traits\FormErrorsTrait,
    AppBundle\Entity\Student\Student,
    AppBundle\Entity\NfcTag\NfcTag;

class PaymentLiqPayAnonymousController extends Controller
{
    use FormErrorsTrait;

    /** @DI\Inject("doctrine.orm.entity_manager") */
    private $_manager;

    /** @DI\Inject("translator") */
    private $_translator;

    /** @DI\Inject("validator") */
    private $_validator;

    /** @DI\Inject("security.authorization_checker") */
    private $_authorizationChecker;

    /** @DI\Inject("security.authentication_utils") */
    private $_authenticationUtils;

    /** @DI\Inject("app.common.messages") */
    private $_messages;

    /** @DI\Inject("app.payment.receipt.parser") */
    private $_paymentReceiptParser;

    /** @DI\Inject("app.payment.liq_pay.anonymous_manager") */
    private $_paymentLiqPayAnonymousManager;

    /**
    * @Method({"GET"})
    * @Route(
    *      "/customer_office/replenish",
    *      name="customer_office_replenish",
    *      host="{domain_website}",
    *      defaults={"_locale" = "%locale_website%", "domain_website" = "%domain_website%"},
    *      requirements={"_locale" = "%locale_website%|ru", "domain_website" = "%domain_website%"}
    * )
    */
    public function paymentLiqPayAnonymousReplenishAction(Request $request)
    {
        if( $this->_authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY') ) {
            return $this->redirectToRoute('customer_office');
        }

        $messages = $this->_messages->getMessages();

        return $this->render('AppBundle:Office/Payment:replenish.html.twig', [
            'messages' => $messages,
        ]);
    }

    /**
    * @Method({"POST"})
    * @Route(
    *      "/customer_office/replenish_check",
    *      name="customer_office_replenish_check",
    *      host="{domain_website}",
    *      defaults={"_locale" = "%locale_website%", "domain_website" = "%domain_website%"},
    *      requirements={"_locale" = "%locale_website%|ru", "domain_website" = "%domain_website%"}
    * )
    */
    public function paymentLiqPayAnonymousReplenishCheckAction(Request $request)
    {
        $isValid = TRUE;

        $validateStudentFullName = function($studentFullName)
        {
            return !empty($studentFullName) ? $studentFullName : FALSE;
        };

        if( !$request->request->has('student_full_name') ||
            !($studentFullName = $validateStudentFullName($request->request->get('student_full_name'))) )
            $isValid = FALSE;

        $validateUserPhoneNumber = function($userPhoneNumber)
        {
            $userPhoneNumberConstraint = new CustomAssert\IsPhoneNumberConstraint;
            $errorList = $this->_validator->validate(
                $userPhoneNumber,
                $userPhoneNumberConstraint
            );

            return ( count($errorList) === 0 ) ? $userPhoneNumber : FALSE;
        };

        if( !$request->request->has('user_phone_number') ||
            !($userPhoneNumber = $validateUserPhoneNumber($request->request->get('user_phone_number'))) )
            $isValid = FALSE;

        $validateNfcTagNumber = function($nfcTagNumber)
        {
            $nfcTagNumber = $this->_paymentReceiptParser->standardizeNfcTagNumber($nfcTagNumber);

            $nfcTagNumberConstraint = new CustomAssert\IsNfcTagNumberConstraint;
            $errorList = $this->_validator->validate(
                $nfcTagNumber,
                $nfcTagNumberConstraint
            );

            return ( count($errorList) === 0 ) ? $nfcTagNumber : FALSE;
        };

        if( !$request->request->has('student_nfc_tag_number') ||
            !($nfcTagNumber = $validateNfcTagNumber($request->request->get('student_nfc_tag_number'))) )
            $isValid = FALSE;

        $validateReplenishAmount = function($replenishAmount)
        {
            $replenishAmount = str_replace(',', '.', str_replace('.', '', $replenishAmount));

            return ( is_numeric($replenishAmount) ) ? $replenishAmount : FALSE;
        };

        if( !$request->request->has('replenish_amount') ||
            !($replenishAmount = $validateReplenishAmount($request->request->get('replenish_amount'))) )
            $isValid = FALSE;

        if( $isValid ) {
            $this->_messages->markPaymentLiqPayAnonymousReplenishSuccess();

            $nfcTag  = (new NfcTag)->setNumber($nfcTagNumber);
            $student = (new Student)
                ->setFullName($studentFullName)
                ->setNfcTag($nfcTag)
            ;

            $orderId     = $this->_paymentLiqPayAnonymousManager->getOrderId($student);
            $description = $this->_paymentLiqPayAnonymousManager->getDescription($student);
            $resultUrl   = $this->_paymentLiqPayAnonymousManager->getResultUrl($student);

            $formParameters = $this->_paymentLiqPayAnonymousManager->getFormParameters(
                $orderId, $replenishAmount, $description, $resultUrl
            );

            $formAction    = $this->_paymentLiqPayAnonymousManager->getCnbFormAction();
            $formData      = $this->_paymentLiqPayAnonymousManager->getCnbFormData($formParameters);
            $formSignature = $this->_paymentLiqPayAnonymousManager->getCnbFormSignature($formParameters);

            return $this->render('AppBundle:Entity/PaymentLiqPay/Form:transferForm.html.twig', [
                'formAction'    => $formAction,
                'formData'      => $formData,
                'formSignature' => $formSignature,
            ]);
        } else {
            $this->_messages->markPaymentLiqPayAnonymousReplenishError();

            return new RedirectResponse($request->headers->get('referer'));
        }

        return new Response('OK');
    }
}
