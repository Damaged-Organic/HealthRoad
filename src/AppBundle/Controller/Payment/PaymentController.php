<?php
// AppBundle/Controller/Payment/PaymentController.php
namespace AppBundle\Controller\Payment;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\RedirectResponse,
    Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Controller\Utility\Traits\FormErrorsTrait,
    AppBundle\Entity\Student\Student,
    AppBundle\Form\Type\Payment\StudentBalanceType;

class PaymentController extends Controller
{
    use FormErrorsTrait;

    public function paymentFormAction(Student $student)
    {
        $form = $this->createForm(new StudentBalanceType, $student, [
            'action' => $this->generateUrl('payment_submit', ['id' => $student->getId()])
        ]);

        return $this->render('AppBundle:Entity/Student/Form:balance.html.twig', [
            'form'    => $form->createView(),
            'student' => $student
        ]);
    }

    /**
     * @Method({"POST"})
     * @Route(
     *      "/payment_submit/{id}",
     *      name="payment_submit",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function paymentSubmitAction(Request $request, $id)
    {
        $_manager = $this->getDoctrine()->getManager();

        $_session = $this->get('session');

        $_translator = $this->get('translator');

        $student = $_manager->getRepository('AppBundle:Student\Student')->find($id);

        if( !$student )
            throw $this->createNotFoundException("Student identified by `id` {$id} not found");

        $form = $this->createForm(new StudentBalanceType, $student);

        $form->handleRequest($request);

        if( !$form->isValid() ) {
            $message = [
                'errors' => $this->getFormErrorMessages($form)
            ];
        } else {
            if( $form->has('replenishLimit') && $form->get('replenishLimit')->getData() ) {
                $student->setTotalLimit(
                    bcadd($student->getTotalLimit(), $form->get('replenishLimit')->getData(), 2)
                );
            }

            $_manager->flush();

            $message = [
                'success' => [$_translator->trans('student_balance.success', [], 'responses')]
            ];
        }

        $_session->getFlashBag()->add('messages', $message);

        return new RedirectResponse($request->headers->get('referer'));
    }
}