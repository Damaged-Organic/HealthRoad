<?php
// AppBundle/Controller/Payment/PaymentController.php
namespace AppBundle\Controller\Payment;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\RedirectResponse,
    Symfony\Bundle\FrameworkBundle\Controller\Controller;

use JMS\DiExtraBundle\Annotation as DI;

use AppBundle\Controller\Utility\Traits\FormErrorsTrait,
    AppBundle\Entity\Student\Student,
    AppBundle\Form\Type\Payment\StudentBalanceType;

class PaymentController extends Controller
{
    use FormErrorsTrait;

    /** @DI\Inject("doctrine.orm.entity_manager") */
    private $_manager;

    /** @DI\Inject("app.common.messages") */
    private $_messages;

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
        $student = $this->_manager->getRepository('AppBundle:Student\Student')->find($id);

        if( !$student )
            throw $this->createNotFoundException("Student identified by `id` {$id} not found");

        $form = $this->createForm(new StudentBalanceType, $student);

        $form->handleRequest($request);

        if( !$form->isValid() ) {
            $this->_messages->markReplenishErrors($this->getFormErrorMessages($form));
        } else {
            if( $form->has('replenishLimit') && $form->get('replenishLimit')->getData() ) {
                $student->setTotalLimit(
                    bcadd($student->getTotalLimit(), $form->get('replenishLimit')->getData(), 2)
                );
            }

            $this->_manager->flush();

            $this->_messages->markReplenishSuccess();
        }

        return new RedirectResponse($request->headers->get('referer'));
    }
}