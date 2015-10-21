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
                'label' => 'product.name_full.label',
                'attr'  => [
                    'placeholder' => 'product.name_full.placeholder'
                ]
            ])
            ->add('nameShort', 'text', [
                'label' => 'product.name_short.label',
                'attr'  => [
                    'placeholder' => 'product.name_short.placeholder'
                ]
            ])
            ->add('code', 'text', [
                'label' => 'product.code.label',
                'attr'  => [
                    'placeholder' => 'product.code.placeholder'
                ]
            ])
            ->add('price', 'number', [
                'scale' => 2,
                'label' => 'product.price.label',
                'attr'  => [
                    'placeholder' => 'product.price.placeholder'
                ]
            ])
            ->add('imageProductFile', 'file', [
                'required' => FALSE,
                'label'    => 'product.image_product_file.label',
                'attr'     => [
                    'accept' => 'image/png, image/jpeg, image/pjpeg, image/gif'
                ]
            ])
            ->add('imageCertificateFile', 'file', [
                'required' => FALSE,
                'label'    => 'product.image_certificate_file.label',
                'attr'     => [
                    'accept' => 'image/png, image/jpeg, image/pjpeg, image/gif'
                ]
            ])
            ->add('supplier', 'entity', [
                'required'     => FALSE,
                'class'        => "AppBundle\\Entity\\Supplier\\Supplier",
                'choice_label' => "name",
                'label'        => 'product.supplier.label',
                'empty_value'  => 'common.choice.placeholder'
            ])
            ->add('manufacturer', 'text', [
                'required' => FALSE,
                'label'    => 'product.manufacturer.label',
                'attr'     => [
                    'placeholder' => 'product.manufacturer.placeholder'
                ]
            ])
            ->add('protein', 'number', [
                'required' => FALSE,
                'scale'    => 2,
                'label'    => 'product.protein.label',
                'attr'     => [
                    'placeholder' => 'product.protein.placeholder'
                ]
            ])
            ->add('fat', 'number', [
                'required' => FALSE,
                'scale'    => 2,
                'label'    => 'product.fat.label',
                'attr'     => [
                    'placeholder' => 'product.fat.placeholder'
                ]
            ])
            ->add('carbohydrate', 'number', [
                'required' => FALSE,
                'scale'    => 2,
                'label'    => 'product.carbohydrate.label',
                'attr'     => [
                    'placeholder' => 'product.carbohydrate.placeholder'
                ]
            ])
            ->add('calories', 'number', [
                'required' => FALSE,
                'label'    => 'product.calories.label',
                'attr'     => [
                    'placeholder' => 'product.calories.placeholder'
                ]
            ])
            ->add('shelfLife', 'text', [
                'required' => FALSE,
                'label'    => 'product.shelf_life.label',
                'attr'     => [
                    'placeholder' => 'product.shelf_life.placeholder'
                ]
            ])
            ->add('storageTemperatureMin', 'number', [
                'required' => FALSE,
                'label'    => 'product.storage_temperature_min.label',
                'attr'     => [
                    'placeholder' => 'product.storage_temperature_min.placeholder'
                ]
            ])
            ->add('storageTemperatureMax', 'number', [
                'required' => FALSE,
                'label'    => 'product.storage_temperature_max.label',
                'attr'     => [
                    'placeholder' => 'product.storage_temperature_max.placeholder'
                ]
            ])
            ->add('weight', 'number', [
                'required' => FALSE,
                'label'    => 'product.weight.label',
                'attr'     => [
                    'placeholder' => 'product.weight.placeholder'
                ]
            ])
            ->add('measurementUnit', 'text', [
                'required' => FALSE,
                'label'    => 'product.measurement_unit.label',
                'attr'     => [
                    'placeholder' => 'product.measurement_unit.placeholder'
                ]
            ])
            ->add('amountInBox', 'number', [
                'required' => FALSE,
                'label'    => 'product.amount_in_box.label',
                'attr'     => [
                    'placeholder' => 'product.amount_in_box.placeholder'
                ]
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