<?php
// src/AppBundle/Form/Type/Activation/ActivationNfcTagType.php
namespace AppBundle\Form\Type\Activation;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\OptionsResolver\OptionsResolver,
    Symfony\Component\Translation\TranslatorInterface;

class ActivationNfcTagType extends AbstractType
{
    private $_translator;

    private $settingNfcTagActivationFee;

    public function __construct(TranslatorInterface $translator, $settingNfcTagActivationFee = NULL)
    {
        $this->_translator = $translator;

        $this->settingNfcTagActivationFee = $settingNfcTagActivationFee;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('activationFee', 'checkbox', [
                'mapped'   => FALSE,
                'required' => FALSE,
                'label'    => $this->_translator->trans('nfc_tag.activation_fee.label', [
                    '%activation_fee%' => $this->settingNfcTagActivationFee
                ], 'forms')
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => 'AppBundle\Entity\NfcTag\NfcTag',
            'translation_domain' => 'forms'
        ]);
    }

    public function getName()
    {
        return 'activation_nfc_tag';
    }
}
