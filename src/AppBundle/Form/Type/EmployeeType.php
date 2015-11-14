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
    private $_translator;

    private $boundlessAccess;
    private $boundedAccess;

    public function __construct(TranslatorInterface $translator, $boundlessAccess, $boundedAccess = NULL)
    {
        $this->_translator = $translator;

        /*
         * TRICKY: $this->boundlessAccess is a string containing exact user role,
         * which also equals TRUE during loose (==) authorization check
         */
        $this->boundlessAccess = $boundlessAccess;
        $this->boundedAccess   = $boundedAccess;
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
                                'label' => 'employee.username.label',
                                'attr'  => [
                                    'placeholder' => 'employee.username.placeholder'
                                ]
                            ])
                        ;
                }
            });

        $builder
            ->add('name', 'text', [
                'required' => FALSE,
                'label'    => 'employee.name.label',
                'attr'     => [
                    'placeholder' => 'employee.name.placeholder'
                ]
            ])
            ->add('surname', 'text', [
                'required' => FALSE,
                'label'    => 'employee.surname.label',
                'attr'     => [
                    'placeholder' => 'employee.surname.placeholder'
                ]
            ])
            ->add('patronymic', 'text', [
                'required' => FALSE,
                'label'    => 'employee.patronymic.label',
                'attr'     => [
                    'placeholder' => 'employee.patronymic.placeholder'
                ]
            ])
            ->add('email', 'email', [
                'required' => FALSE,
                'label'    => 'employee.email.label',
                'attr'     => [
                    'placeholder' => 'employee.email.placeholder'
                ]
            ])
            ->add('phoneNumber', 'text', [
                'required' => FALSE,
                'label'    => 'employee.phone_number.label',
                'attr'     => [
                    'placeholder' => 'employee.phone_number.placeholder'
                ]
            ])
            ->add('skypeName', 'text', [
                'required' => FALSE,
                'label'    => 'employee.skype_name.label',
                'attr'     => [
                    'placeholder' => 'employee.skype_name.placeholder'
                ]
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
                        ->add('employeeGroup', 'text', [
                            'required'   => FALSE,
                            'read_only'  => TRUE,
                            'disabled'   => TRUE,
                            'data_class' => 'AppBundle\Entity\Employee\EmployeeGroup',
                            'label'      => 'employee.employee_group.label'
                        ])
                        ->add('password', 'repeated', [
                            'required'       => FALSE,
                            'first_name'     => "password",
                            'second_name'    => "password_confirm",
                            'type'           => "password",
                            'first_options'  => [
                                'label' => 'employee.password.label',
                                'attr'  => [
                                    'placeholder' => 'employee.password.placeholder'
                                ]
                            ],
                            'second_options' => [
                                'label' => 'employee.password_confirm.label',
                                'attr'  => [
                                    'placeholder' => 'employee.password_confirm.placeholder'
                                ]
                            ]
                        ])
                    ;

                    if( $this->boundedAccess ) {
                        $form
                            ->add('isEnabled', 'checkbox', [
                                'required' => FALSE,
                                'label'    => 'employee.is_enabled.label'
                            ])
                        ;
                    }

                    $form->add('update', 'submit', ['label' => 'common.update.label']);

                    if( $this->boundlessAccess )
                        $form->add('update_and_return', 'submit', ['label' => 'common.update_and_return.label']);
                } else {
                    $form
                        ->add('employeeGroup', 'entity', [
                            'class'           => "AppBundle\\Entity\\Employee\\EmployeeGroup",
                            'empty_data'      => 0,
                            'choice_label'    => "name",
                            'label'           => 'employee.employee_group.label',
                            'empty_value'     => 'common.choice.placeholder',
                            'invalid_message' => $this->_translator->trans('employee.employee_group.invalid_massage', [], 'validators'),
                            'query_builder'   => function (EmployeeGroupRepository $repository) {
                                return $repository->getSubordinateRolesQuery($this->boundlessAccess);
                            }
                        ])
                        ->add('password', 'repeated', [
                            'required'      => TRUE,
                            'first_name'    => "password",
                            'second_name'   => "password_confirm",
                            'type'          => "password",
                            'first_options' => [
                                'label' => 'employee.password.label',
                                'attr'  => [
                                    'data-rule-required' => "true",
                                    'data-msg-required'  => $this->_translator->trans('employee.password.not_blank', [], 'validators'),
                                    'placeholder'        => 'employee.password.placeholder'
                                ]
                            ],
                            'second_options' => [
                                'label' => 'employee.password_confirm.label',
                                'attr'  => [
                                    'data-rule-required' => "true",
                                    'data-msg-required'  => $this->_translator->trans('employee.password_confirm.not_blank', [], 'validators'),
                                    'placeholder'        => 'employee.password_confirm.placeholder'
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
            'data_class'         => 'AppBundle\Entity\Employee\Employee',
            'translation_domain' => 'forms'
        ]);
    }

    public function getName()
    {
        return 'employee';
    }
}