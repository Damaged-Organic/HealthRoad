<?php
// AppBundle/Form/Type/SupplierType.php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\Form\FormEvent,
    Symfony\Component\Form\FormEvents,
    Symfony\Component\OptionsResolver\OptionsResolver;

class SupplierType extends AbstractType
{
    private $boundlessAccess;

    public function __construct($boundlessAccess)
    {
        $this->boundlessAccess = $boundlessAccess;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', [
                'label' => "Name"
            ])
            ->add('nameLegal', 'text', [
                'label' => "Name Legal"
            ])
            ->add('description', 'text', [
                'label' => "Description"
            ])
            ->add('logoFile', 'file', [
                'required' => FALSE,
                'label'    => "Logo image"
            ])
            ->add('phoneNumberSupplier', 'text', [
                'required' => FALSE,
                'label'    => "Supplier phone number"
            ])
            ->add('emailSupplier', 'email', [
                'required' => FALSE,
                'label'    => "Supplier email"
            ])
            ->add('nameContact', 'text', [
                'required' => FALSE,
                'label'    => "Contact name"
            ])
            ->add('phoneNumberContact', 'text', [
                'required' => FALSE,
                'label'    => "Product image"
            ])
            ->add('emailContact', 'email', [
                'required' => FALSE,
                'label'    => "Contact email"
            ])
            ->add('contractNumber', 'text', [
                'required' => FALSE,
                'label'    => "Contract number"
            ])
            ->add('contractDateStart', 'date', [
                'required' => FALSE,
                'label'    => "Contract date start"
            ])
            ->add('contractDateEnd', 'date', [
                'required' => FALSE,
                'label'    => "Contract date end"
            ])
        ;

        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event)
            {
                $supplier = $event->getData();

                $form = $event->getForm();

                if( $supplier && $supplier->getId() !== NULL )
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
            'data_class'         => 'AppBundle\Entity\Supplier\Supplier',
            'translation_domain' => 'forms'
        ]);
    }

    public function getName()
    {
        return 'supplier';
    }
}