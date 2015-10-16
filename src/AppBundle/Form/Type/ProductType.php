<?php
// AppBundle/Form/Type/ProductType.php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\Form\FormEvent,
    Symfony\Component\Form\FormEvents,
    Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    private $boundlessAccess;

    public function __construct($boundlessAccess)
    {
        $this->boundlessAccess = $boundlessAccess;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nameFull', 'text', [
                'label' => "Full name"
            ])
            ->add('nameShort', 'text', [
                'label' => "Short name"
            ])
            ->add('imageProductFile', 'file', [
                'required' => FALSE,
                'label'    => "Product image"
            ])
            ->add('imageCertificateFile', 'file', [
                'required' => FALSE,
                'label'    => "Product certificate"
            ])
            ->add('code', 'text', [
                'label' => "Code"
            ])
            ->add('price', 'number', [
                'label' => "Price",
                'scale' => 2
            ])
            ->add('manufacturer', 'text', [
                'label' => "Manufacturer"
            ])
            ->add('calories', 'number', [
                'label' => "Calories"
            ])
            ->add('shelfLife', 'text', [
                'label' => "Shelf life"
            ])
            ->add('storageTemperatureMin', 'number', [
                'label' => "Storage temperature (Min)"
            ])
            ->add('storageTemperatureMax', 'number', [
                'label' => "Storage temperature (Max)"
            ])
            ->add('weigth', 'number', [
                'label' => "Weigth"
            ])
            ->add('measurementUnit', 'text', [
                'label' => "Measurement unit"
            ])
            ->add('amountInBox', 'number', [
                'label' => "Amount in box"
            ])
            ->add('supplier', 'entity', [
                'required'     => FALSE,
                'class'        => "AppBundle\\Entity\\Supplier\\Supplier",
                'choice_label' => "name",
                'placeholder'  => "Choose supplier"
            ])
        ;

        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event)
            {
                $productType = $event->getData();

                $form = $event->getForm();

                if( $productType && $productType->getId() !== NULL )
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
            'data_class'         => 'AppBundle\Entity\Product\Product',
            'translation_domain' => 'forms'
        ]);
    }

    public function getName()
    {
        return 'product';
    }
}