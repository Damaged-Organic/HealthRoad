<?php
// AppBundle/Service/Website/FeedbackMailer.php
namespace AppBundle\Service\Website;

use AppBundle\Entity\Website\Feedback\Feedback;
use AppBundle\Entity\Website\Feedback\FeedbackOrder;
use AppBundle\Entity\Website\Feedback\FeedbackSupplier;
use AppBundle\Service\Common\MailerShortcut;

use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\Translation\TranslatorInterface;

class FeedbackMailer
{
    private $_mailerShortcut;
    private $_translator;
    private $_twigEngine;

    private $_emailParameters;

    public function setMailerShortcut(MailerShortcut $mailerShortcut)
    {
        $this->_mailerShortcut = $mailerShortcut;
    }

    public function setTranslator(TranslatorInterface $translator)
    {
        $this->_translator = $translator;
    }

    public function setTemplating(TwigEngine $twigEngine)
    {
        $this->_twigEngine = $twigEngine;
    }

    public function setEmailParameters($emailParameters)
    {
        $this->_emailParameters = $emailParameters;
    }

    public function sendFeedback(Feedback $feedback)
    {
        $subject = $this->_translator->trans('website.feedback.subject', [], 'emails');

        $body = $this->_twigEngine->render('AppBundle:Website/Email:feedback.html.twig', [
            'subject'  => $subject,
            'feedback' => $feedback
        ]);

        return $this->_mailerShortcut->sendMail([$this->_emailParameters['no_reply'] => "Зворотній зв’язок системи \"Дорога Здоров’я\""], $this->_emailParameters['feedback'], $subject, $body);
    }

    public function sendFeedbackOrder(FeedbackOrder $feedbackOrder)
    {
        $subject = $this->_translator->trans('website.feedback_order.subject', [], 'emails');

        $body = $this->_twigEngine->render('AppBundle:Website/Email:feedbackOrder.html.twig', [
            'subject'       => $subject,
            'feedbackOrder' => $feedbackOrder
        ]);

        return $this->_mailerShortcut->sendMail([$this->_emailParameters['no_reply'] => "Зворотній зв’язок системи \"Дорога Здоров’я\""], $this->_emailParameters['feedback'], $subject, $body);
    }

    public function sendFeedbackSupplier(FeedbackSupplier $feedbackSupplier)
    {
        $subject = $this->_translator->trans('website.feedback_supplier.subject', [], 'emails');

        $body = $this->_twigEngine->render('AppBundle:Website/Email:feedbackSupplier.html.twig', [
            'subject'          => $subject,
            'feedbackSupplier' => $feedbackSupplier
        ]);

        return $this->_mailerShortcut->sendMail([$this->_emailParameters['no_reply'] => "Зворотній зв’язок системи \"Дорога Здоров’я\""], $this->_emailParameters['feedback'], $subject, $body);
    }
}