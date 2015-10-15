<?php
// AppBundle/Form/Type/EmployeeType.php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\Form\FormEvent,
    Symfony\Component\Form\FormEvents,
    Symfony\Component\OptionsResolver\OptionsResolver,
    Symfony\Component\Translation\TranslatorInterface;

use AppBundle\Entity\Employee\Repository\EmployeeGroupRepository;

class EmployeeType extends AbstractType
{
    private $boundlessAccess;
    private $boundedAccess;

    private $_translator;

    public function __construct(TranslatorInterface $translator, $boundlessAccess, $boundedAccess = NULL)
    {
        /*
         * TRICKY: $this->boundlessAccess is a string containing exact user role,
         * which also equals TRUE during loose (==) authorization check
         */
        $this->boundlessAccess = $boundlessAccess;
        $this->boundedAccess   = $boundedAccess;

        $this->_translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event)
            {
                $employee = $event->getData();

                $form = $event->getForm();

                if( ($employee && $employee->getId() === NULL && $this->boundlessAccess) ||
                    ($employee && $employee->getId() !== NULL && $this->boundedAccess)) {
                        $form
                            ->add('username', 'text', [
                                'label' => "Username *"
                            ])
                            ->add('employeeGroup', 'entity', [
                                'class' => "AppBundle\\Entity\\Employee\\EmployeeGroup",
                                'query_builder' => function (EmployeeGroupRepository $repository) {
                                    return $repository->getSubordinateRolesQuery($this->boundlessAccess);
                                },
                                'choice_label' => "name",
                                'label' => "Role",
                                'empty_value' => "Choose a role"
                            ]);
                }
            });

        $builder
            ->add('name', 'text', [
                'required' => FALSE,
                'label'    => "Name"
            ])
            ->add('surname', 'text', [
                'required' => FALSE,
                'label'    => "Surname"
            ])
            ->add('patronymic', 'text', [
                'required' => FALSE,
                'label'    => "Patronymic"
            ])
            ->add('email', 'email', [
                'required' => FALSE,
                'label'    => "Email"
            ])
            ->add('phoneNumber', 'text', [
                'required' => FALSE,
                'label'    => "Phone number"
            ])
            ->add('skypeName', 'text', [
                'required' => FALSE,
                'label'    => "Skype"
            ])
        ;

        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event)
            {
                $employee = $event->getData();

                $form = $event->getForm();

                if( $employee && $employee->getId() !== NULL )
                {
                    $form
                        ->add('password', 'repeated', [
                            'required'       => FALSE,
                            'first_name'     => "password",
                            'second_name'    => "password_confirm",
                            'type'           => "password",
                            'first_options'  => [
                                'label' => 'Password'
                            ],
                            'second_options' => [
                                'label' => 'Repeat Password'
                            ]
                        ])
                    ;

                    if( $this->boundedAccess ) {
                        $form
                            ->add('isEnabled', 'checkbox', [
                                'required' => FALSE,
                                'label' => "Is enabled"
                            ])
                        ;
                    }

                    $form
                        ->add('update', 'submit', [
                            'label' => "Сохранить"
                        ])
                    ;

                    if( $this->boundlessAccess ) {
                        $form
                            ->add('update_and_return', 'submit', [
                                'label' => "Сохранить и вернуться к списку"
                            ])
                        ;
                    }
                } else {
                    $form
                        ->add('password', 'repeated', [
                            'required'      => TRUE,
                            'first_name'    => "password",
                            'second_name'   => "password_confirm",
                            'type'          => "password",
                            'first_options' => [
                                'label' => 'Password *',
                                'attr'  => [
                                    'data-rule-required' => "true",
                                    'data-msg-required'  => $this->_translator->trans('employee.password.not_blank', [], 'validators')
                                ]
                            ],
                            'second_options' => [
                                'label' => 'Repeat Password *',
                                'attr'  => [
                                    'data-rule-required' => "true",
                                    'data-msg-required'  => $this->_translator->trans('employee.password_confirm.not_blank', [], 'validators')
                                ]
                            ]
                        ])
                        ->add('create', 'submit', [
                            'label' => "Создать"
                        ])
                    ;

                    if( $this->boundlessAccess ) {
                        $form
                            ->add('create_and_return', 'submit', [
                                'label' => "Создать и вернуться к списку"
                            ])
                        ;
                    }
                }
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => 'AppBundle\Entity\Employee\Employee',
            'translation_domain' => 'forms'
        ]);
    }

    public function getName()
    {
        return 'employee';
    }
}