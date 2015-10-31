<?php
// AppBundle/Form/Type/VendingMachineType.php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\Form\FormEvent,
    Symfony\Component\Form\FormEvents,
    Symfony\Component\OptionsResolver\OptionsResolver;

class VendingMachineType extends AbstractType
{
    private $boundlessAccess;

    public function __construct($boundlessAccess)
    {
        $this->boundlessAccess = $boundlessAccess;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('serial', 'text', [
                'label' => 'vending_machine.serial.label',
                'attr'  => [
                    'placeholder' => 'vending_machine.serial.placeholder'
                ]
            ])
            ->add('login', 'text', [
                'required' => FALSE,
                'label'    => 'vending_machine.login.label',
                'attr'     => [
                    'placeholder' => 'vending_machine.login.placeholder'
                ]
            ])
            ->add('password', 'text', [
                'required' => FALSE,
                'label'    => 'vending_machine.password.label',
            ])
            ->add('school', 'entity', [
                'required'     => FALSE,
                'class'        => "AppBundle\\Entity\\School\\School",
                'choice_label' => "name",
                'label'        => 'vending_machine.school.label',
                'empty_value'  => 'common.choice.placeholder'
            ])
            ->add('productVendingGroup', 'entity', [
                'required'     => FALSE,
                'class'        => "AppBundle\\Entity\\Product\\ProductVendingGroup",
                'choice_label' => "name",
                'label'        => 'vending_machine.product_vending_group.label',
                'empty_value'  => 'common.choice.placeholder'
            ])
            ->add('name', 'text', [
                'required' => FALSE,
                'label'    => 'vending_machine.name.label',
                'attr'     => [
                    'placeholder' => 'vending_machine.name.placeholder'
                ]
            ])
            ->add('nameTechnician', 'text', [
                'required' => FALSE,
                'label'    => 'vending_machine.name_technician.label',
                'attr'     => [
                    'placeholder' => 'vending_machine.name_technician.placeholder'
                ]
            ])
            ->add('numberShelves', 'number', [
                'required' => FALSE,
                'label'    => 'vending_machine.number_shelves.label',
                'attr'     => [
                    'placeholder' => 'vending_machine.number_shelves.placeholder'
                ]
            ])
            ->add('numberSprings', 'text', [
                'required' => FALSE,
                'label'    => 'vending_machine.number_springs.label',
                'attr'     => [
                    'placeholder' => 'vending_machine.number_springs.placeholder'
                ]
            ])
        ;

        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event)
            {
                $vendingMachine = $event->getData();

                $form = $event->getForm();

                if( $vendingMachine && $vendingMachine->getId() !== NULL )
                {
                    $form->add('update', 'submit', ['label' => 'common.update.label']);

                    if( $this->boundlessAccess )
                        $form->add('update_and_return', 'submit', ['label' => 'common.update_and_return.label']);
                } else {
                    $form->add('create', 'submit', ['label' => 'common.create.label']);

                    if( $this->boundlessAccess )
                        $form->add('create_and_return', 'submit', ['label' => 'common.create_and_return.label']);
                }
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => 'AppBundle\Entity\VendingMachine\VendingMachine',
            'translation_domain' => 'forms'
        ]);
    }

    public function getName()
    {
        return 'vending_machine';
    }
}