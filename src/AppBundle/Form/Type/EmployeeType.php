<?php
// AppBundle/Form/Type/EmployeeType.php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\Form\FormEvent,
    Symfony\Component\Form\FormEvents,
    Symfony\Component\OptionsResolver\OptionsResolver;

use AppBundle\Entity\Employee\Repository\EmployeeGroupRepository;

class EmployeeType extends AbstractType
{
    private $boundlessAccess;
    private $boundedAccess;

    public function __construct($boundlessAccess, $boundedAccess = NULL)
    {
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
                                'label' => "Username"
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
                            'required'    => FALSE,
                            'first_name'  => "Password",
                            'second_name' => "Password_confirm",
                            'type'        => "password",
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
                            'required'    => TRUE,
                            'first_name'  => "Password",
                            'second_name' => "Password_confirm",
                            'type'        => "password",
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