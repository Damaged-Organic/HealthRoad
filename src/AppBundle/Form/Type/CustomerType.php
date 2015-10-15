<?php
// AppBundle/Form/Type/CustomerType.php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\Form\FormEvent,
    Symfony\Component\Form\FormEvents,
    Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerType extends AbstractType
{
    private $boundlessAccess;

    public function __construct($boundlessAccess)
    {
        $this->boundlessAccess = $boundlessAccess;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('phoneNumber', 'text', [
                'label' => "Phone number"
            ])
            ->add('name', 'text', [
                'label' => "Name"
            ])
            ->add('surname', 'text', [
                'label' => "Surname"
            ])
            ->add('patronymic', 'text', [
                'label' => "Patronymic"
            ])
            ->add('email', 'email', [
                'label' => "Email"
            ])
            ->add('isEnabled', 'checkbox', [
                'required' => FALSE,
                'label' => "Is enabled"
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
                            'required'    => FALSE,
                            'first_name'  => "password",
                            'second_name' => "password_confirm",
                            'type'        => "password",
                        ])
                    ;

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
                            'first_name'  => "password",
                            'second_name' => "password_confirm",
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
            'data_class'         => 'AppBundle\Entity\Customer\Customer',
            'translation_domain' => 'forms'
        ]);
    }

    public function getName()
    {
        return 'customer';
    }
}