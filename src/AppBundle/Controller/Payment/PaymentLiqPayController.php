<?php
// src/AppBundle/Controller/Payment/PaymentLiqPayController.php
namespace AppBundle\Controller\Payment;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Bundle\FrameworkBundle\Controller\Controller;

use JMS\DiExtraBundle\Annotation as DI;

use AppBundle\Security\Authorization\Voter\StudentVoter;

class PaymentLiqPayController extends Controller
{
    /** @DI\Inject("doctrine.orm.entity_manager") */
    private $_manager;

    /** @DI\Inject("translator") */
    private $_translator;

    /** @DI\Inject("app.payment.liq_pay.manager") */
    private $_paymentLiqPayManager;

    /**
     * @Method({"GET", "POST"})
     * @Route(
     *      "/payment_liq_pay/replenish/submit/{id}",
     *      name="payment_liq_pay_replenish_submit",
     *      host="{domain_website}",
     *      defaults={"_locale" = "%locale%", "domain_website" = "%domain_website%"},
     *      requirements={"_locale" = "%locale%", "domain_website" = "%domain_website%", "id" = "\d+"}
     * )
     */
    public function paymentLiqPayReplenishSubmitAction(Request $request, $id)
    {
        if( $request->isMethod('GET') )
            return $this->redirectToRoute('customer_office_students', ['id' => $id]);

        $validateReplenishAmount = function($replenishAmount)
        {
            $replenishAmount = str_replace(',', '.', str_replace('.', '', $replenishAmount));

            return ( is_numeric($replenishAmount) ) ? $replenishAmount : FALSE;
        };

        $student = $this->_manager->getRepository('AppBundle:Student\Student')->find($id);

        if( !$student )
            throw $this->createNotFoundException("Student identified by `id` {$id} not found");

        if( !$this->isGranted(StudentVoter::STUDENT_TOTAL_LIMIT_REPLENISH, $student) )
            return $this->redirectToRoute('customer_office_students', ['id' => $id]);

        if( !$request->request->has('replenish_amount') )
            return $this->redirectToRoute('customer_office_students', ['id' => $id]);

        if( !($replenishAmount = $validateReplenishAmount($request->request->get('replenish_amount'))) )
            return $this->redirectToRoute('customer_office_students', ['id' => $id]);

        $orderId     = $this->_paymentLiqPayManager->getOrderId($student);
        $description = $this->_paymentLiqPayManager->getDescription($student);
        $resultUrl   = $this->_paymentLiqPayManager->getResultUrl($student);

        $formParameters = $this->_paymentLiqPayManager->getFormParameters(
            $orderId, $replenishAmount, $description, $resultUrl
        );

        $formAction    = $this->_paymentLiqPayManager->getCnbFormAction();
        $formData      = $this->_paymentLiqPayManager->getCnbFormData($formParameters);
        $formSignature = $this->_paymentLiqPayManager->getCnbFormSignature($formParameters);

        return $this->render('AppBundle:Entity/PaymentLiqPay/Form:transferForm.html.twig', [
            'formAction'    => $formAction,
            'formData'      => $formData,
            'formSignature' => $formSignature
        ]);
    }
}
