<?php
// AppBundle/Form/Type/Website/FeedbackType.php
namespace AppBundle\Form\Type\Website;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\OptionsResolver\OptionsResolver,
    Symfony\Component\Translation\TranslatorInterface;

class FeedbackType extends AbstractType
{
    private $_translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->_translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', [
                'label' => 'website.feedback.name.label',
                'attr' => [
                    'placeholder'         => 'website.feedback.name.placeholder',
                    'data-rule-required'  => 'true',
                    'data-msg-required'   => $this->_translator->trans('website.feedback.name.not_blank', [], 'validators'),
                    'data-rule-minlength' => 2,
                    'data-msg-minlength'  => $this->_translator->trans('website.feedback.name.length.min', [], 'validators'),
                    'data-rule-maxlength' => 250,
                    'data-msg-maxlength'  => $this->_translator->trans('website.feedback.name.length.max', [], 'validators')
                ]
            ])
            ->add('email', 'email', [
                'label' => 'website.feedback.email.label',
                'attr' => [
                    'placeholder'        => 'website.feedback.email.placeholder',
                    'data-rule-required' => 'true',
                    'data-msg-required'  => $this->_translator->trans('website.feedback.email.not_blank', [], 'validators'),
                    'data-rule-email'    => 'true',
                    'data-msg-email'     => $this->_translator->trans('website.feedback.email.valid', [], 'validators')
                ]
            ])
            ->add('phoneNumber', 'text', [
                'label' => 'website.feedback.phone_number.label',
                'attr' => [
                    'placeholder'        => 'website.feedback.phone_number.placeholder',
                    'data-rule-required' => 'true',
                    'data-msg-required'  => $this->_translator->trans('website.feedback.phone_number.not_blank', [], 'validators'),
                    'data-mask'          => '+38 (099) 999-99-99'
                ]
            ])
            ->add('message', 'textarea', [
                'label' => 'website.feedback.message.label',
                'attr' => [
                    'placeholder'         => 'website.feedback.message.placeholder',
                    'data-rule-required'  => 'true',
                    'data-msg-required'   => $this->_translator->trans('website.feedback.message.not_blank', [], 'validators'),
                    'data-rule-minlength' => 5,
                    'data-msg-minlength'  => $this->_translator->trans('website.feedback.message.length.min', [], 'validators'),
                    'data-rule-maxlength' => 1500,
                    'data-msg-maxlength'  => $this->_translator->trans('website.feedback.message.length.max', [], 'validators')
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => 'AppBundle\Entity\Website\Feedback\Feedback',
            'translation_domain' => 'forms'
        ]);
    }

    public function getName()
    {
        return 'feedback';
    }
}