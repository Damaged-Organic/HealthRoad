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
                'label' => "Serial *"
            ])
            ->add('login', 'text', [
                'label'    => "API Login",
                'required' => FALSE,
            ])
            ->add('password', 'text', [
                'label'    => "API Password",
                'required' => FALSE,
            ])
            ->add('school', 'entity', [
                'required'     => FALSE,
                'class'        => "AppBundle\\Entity\\School\\School",
                'choice_label' => "name",
                'placeholder'  => "Choose school"
            ])
            ->add('productVendingGroup', 'entity', [
                'required'     => FALSE,
                'class'        => "AppBundle\\Entity\\Product\\ProductVendingGroup",
                'choice_label' => "name",
                'placeholder'  => "Choose product vending group"
            ])
            ->add('name', 'text', [
                'label'    => "Name",
                'required' => FALSE
            ])
            ->add('nameTechnician', 'text', [
                'label'    => "Technician name",
                'required' => FALSE
            ])
            ->add('numberShelves', 'number', [
                'label'    => "Shelves number",
                'required' => FALSE
            ])
            ->add('numberSprings', 'text', [
                'label'    => "Springs number",
                'required' => FALSE
            ])
        ;

        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event)
            {
                $vendingMachine = $event->getData();

                $form = $event->getForm();

                if( $vendingMachine && $vendingMachine->getId() !== NULL )
                {
                    $form->add('update', 'submit', ['label' => "Сохранить"]);

                    if( $this->boundlessAccess )
                        $form->add('update_and_return', 'submit', ['label' => "Сохранить и вернуться к списку"]);
                } else {
                    $form->add('create', 'submit', ['label' => "Создать"]);

                    if( $this->boundlessAccess )
                        $form->add('create_and_return', 'submit', ['label' => "Создать и вернуться к списку"]);
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