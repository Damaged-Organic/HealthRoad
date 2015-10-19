<?php
// AppBundle/Form/Type/CustomerType.php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\Form\FormEvent,
    Symfony\Component\Form\FormEvents,
    Symfony\Component\OptionsResolver\OptionsResolver,
    Symfony\Component\Translation\TranslatorInterface;

class CustomerType extends AbstractType
{
    private $_translator;

    private $boundlessAccess;

    public function __construct(TranslatorInterface $translator, $boundlessAccess)
    {
        $this->_translator = $translator;

        $this->boundlessAccess = $boundlessAccess;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('phoneNumber', 'text', [
                'label' => 'customer.phone_number.label',
                'attr'  => [
                    'placeholder' => 'customer.phone_number.placeholder'
                ]
            ])
            ->add('name', 'text', [
                'label' => 'customer.name.label',
                'attr'  => [
                    'placeholder' => 'customer.name.placeholder'
                ]
            ])
            ->add('surname', 'text', [
                'label' => 'customer.surname.label',
                'attr'  => [
                    'placeholder' => 'customer.surname.placeholder'
                ]
            ])
            ->add('patronymic', 'text', [
                'label' => 'customer.patronymic.label',
                'attr'  => [
                    'placeholder' => 'customer.patronymic.placeholder'
                ]
            ])
            ->add('email', 'email', [
                'required' => FALSE,
                'label'    => 'customer.email.label',
                'attr'     => [
                    'placeholder' => 'customer.email.placeholder'
                ]
            ])
        ;

        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event)
            {
                $customer = $event->getData();

                $form = $event->getForm();

                if ($customer && $customer->getId() !== NULL)
                {
                    $form
                        ->add('password', 'repeated', [
                            'required'       => FALSE,
                            'first_name'     => "password",
                            'second_name'    => "password_confirm",
                            'type'           => "password",
                            'first_options'  => [
                                'label' => 'customer.password.label',
                                'attr'  => [
                                    'placeholder' => 'customer.password.placeholder'
                                ]
                            ],
                            'second_options' => [
                                'label' => 'customer.password_confirm.label',
                                'attr'  => [
                                    'placeholder' => 'customer.password_confirm.placeholder'
                                ]
                            ]
                        ])
                        ->add('isEnabled', 'checkbox', [
                            'required' => FALSE,
                            'label'    => 'customer.is_enabled.label'
                        ])
                    ;

                    $form->add('update', 'submit', ['label' => 'common.update.label']);

                    if( $this->boundlessAccess )
                        $form->add('update_and_return', 'submit', ['label' => 'common.update_and_return.label']);
                } else {
                    $form
                        ->add('password', 'repeated', [
                            'required'    => TRUE,
                            'first_name'  => "password",
                            'second_name' => "password_confirm",
                            'type'        => "password",
                            'first_options' => [
                                'label' => 'customer.password.label',
                                'attr'  => [
                                    'data-rule-required' => "true",
                                    'data-msg-required'  => $this->_translator->trans('customer.password.not_blank', [], 'validators'),
                                    'placeholder'        => 'customer.password.placeholder'
                                ]
                            ],
                            'second_options' => [
                                'label' => 'customer.password_confirm.label',
                                'attr'  => [
                                    'data-rule-required' => "true",
                                    'data-msg-required'  => $this->_translator->trans('customer.password_confirm.not_blank', [], 'validators'),
                                    'placeholder'        => 'customer.password_confirm.placeholder'
                                ]
                            ]
                        ])
                        ->add('create', 'submit', ['label' => 'common.create.label'])
                    ;

                    if( $this->boundlessAccess )
                        $form->add('create_and_return', 'submit', ['label' => 'common.create_and_return.label']);
                }
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => 'AppBundle\Entity\Customer\Customer',
            'translation_domain' => 'forms'
        ]);
    }

    public function getName()
    {
        return 'customer';
    }
}