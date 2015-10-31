<?php
// AppBundle/Form/Type/RegionType.php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\Form\FormEvent,
    Symfony\Component\Form\FormEvents,
    Symfony\Component\OptionsResolver\OptionsResolver;

class RegionType extends AbstractType
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
                'label' => 'region.name.label',
                'attr'  => [
                    'placeholder' => 'region.name.placeholder'
                ]
            ])
            ->add('administrativeCenter', 'text', [
                'required' => FALSE,
                'label'    => 'region.administrative_center.label',
                'attr'     => [
                    'placeholder' => 'region.administrative_center.placeholder'
                ]
            ])
            ->add('phoneCode', 'text', [
                'required' => FALSE,
                'label'    => 'region.phone_code.label',
                'attr'     => [
                    'placeholder' => 'region.phone_code.placeholder'
                ]
            ])
        ;

        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event)
            {
                $region = $event->getData();

                $form = $event->getForm();

                if( $region && $region->getId() !== NULL )
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
            'data_class'         => 'AppBundle\Entity\Region\Region',
            'translation_domain' => 'forms'
        ]);
    }

    public function getName()
    {
        return 'region';
    }
}