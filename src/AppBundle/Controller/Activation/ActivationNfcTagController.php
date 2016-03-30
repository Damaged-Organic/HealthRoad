<?php
// src/AppBundle/Controller/Activation/ActivationNfcTagController.php
namespace AppBundle\Controller\Activation;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\RedirectResponse,
    Symfony\Bundle\FrameworkBundle\Controller\Controller;

use JMS\DiExtraBundle\Annotation as DI;

use AppBundle\Controller\Utility\Traits\FormErrorsTrait,
    AppBundle\Entity\NfcTag\NfcTag,
    AppBundle\Security\Authorization\Voter\NfcTagVoter,
    AppBundle\Form\Type\Activation\ActivationNfcTagType;

class ActivationNfcTagController extends Controller
{
    use FormErrorsTrait;

    /** @DI\Inject("doctrine.orm.entity_manager") */
    private $_manager;

    /** @DI\Inject("app.common.messages") */
    private $_messages;

    /** @DI\Inject("translator") */
    private $_translator;

    /** @DI\Inject("app.purchase_service.manager") */
    private $_purchaseServiceManager;

    public function activationNfcTagWidgetAction(NfcTag $nfcTag)
    {
        if( $nfcTag->getIsActivated() ) {
            $view = $this->activationNfcTagInfoAction($nfcTag);
        } else {
            $view = $this->activationNfcTagFormAction($nfcTag);
        }

        return $view;
    }

    public function activationNfcTagFormAction(NfcTag $nfcTag)
    {
        $settingNfcTagActivationFee = $this->_manager->getRepository('AppBundle:Setting\Setting')
            ->findNfcTagActivationFee()
            ->getSettingValue()
        ;

        $activationNfcTagType = new ActivationNfcTagType($this->_translator, $settingNfcTagActivationFee);

        $form = $this->createForm($activationNfcTagType, $nfcTag, [
            'action' => $this->generateUrl('activation_nfc_tag_submit', ['id' => $nfcTag->getId()])
        ]);

        return $this->render('AppBundle:Entity/ActivationNfcTag/Form:activationNfcTag.html.twig', [
            'form' => $form->createView()
        ]);
    }

    public function activationNfcTagInfoAction(NfcTag $nfcTag)
    {
        return $this->render('AppBundle:Entity/ActivationNfcTag/Info:activationNfcTag.html.twig', [
            'nfcTag' => $nfcTag
        ]);
    }

    /**
     * @Method({"POST"})
     * @Route(
     *      "/activation/nfc_tag/submit/{id}",
     *      name="activation_nfc_tag_submit",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function paymentManualReplenishSubmitAction(Request $request, $id)
    {
        $nfcTag = $this->_manager->getRepository('AppBundle:NfcTag\NfcTag')->find($id);

        if( !$nfcTag )
            throw $this->createNotFoundException("Nfc Tag identified by `id` {$id} not found");

        if( !$this->isGranted(NfcTagVoter::NFC_TAG_ACTIVATE, $nfcTag) )
            throw $this->createAccessDeniedException();

        $student = $nfcTag->getStudent();

        if( !$student )
            throw $this->createNotFoundException("Student is not bound to Nfc Tag identified by `id` {$id}");

        $activationNfcTagType = new ActivationNfcTagType($this->_translator);

        $form = $this->createForm($activationNfcTagType, $nfcTag);

        $form->handleRequest($request);

        if( !$form->isValid() ) {
            $this->_messages->markActivationNfcTagErrors($this->getFormErrorMessages($form));
        } else {
            $isCharged = $form->has('activationFee') && $form->get('activationFee')->getData();

            if( !$isCharged ) {
                $this->_purchaseServiceManager->freeActivationNfcTag($student);

                $this->_messages->markActivationNfcTagWarning();
            } else {
                $settingNfcTagActivationFee = $this->_manager->getRepository('AppBundle:Setting\Setting')
                    ->findNfcTagActivationFee();

                if( !$this->_purchaseServiceManager->validateStatusActivationNfcTag($student) ) {
                        $this->_messages->markActivationNfcTagErrors([
                            [$this->_translator->trans('activation.nfc_tag.error.activated', [], 'responses')]
                        ]);
                } elseif( !$this->_purchaseServiceManager->validateBalanceActivationNfcTag($student, $settingNfcTagActivationFee) ) {
                    $this->_messages->markActivationNfcTagErrors([
                        [$this->_translator->trans('activation.nfc_tag.error.insufficient', [], 'responses')]
                    ]);
                } else {
                    $this->_purchaseServiceManager->purchaseActivationNfcTag($student, $settingNfcTagActivationFee);

                    $this->_messages->markActivationNfcTagSuccess();
                }
            }
        }

        return new RedirectResponse($request->headers->get('referer'));
    }
}
