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
                'label' => "Name *"
            ])
            ->add('administrativeCenter', 'text', [
                'required' => FALSE,
                'label'    => "Administrative Center"
            ])
            ->add('phoneCode', 'text', [
                'required' => FALSE,
                'label'    => "Phone Code"
            ])
        ;

        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event)
            {
                $region = $event->getData();

                $form = $event->getForm();

                if( $region && $region->getId() !== NULL )
                {
                    $form
                        ->add('update', 'submit', [
                            'label' => "Сохранить"
                        ])
                    ;

                    if( $this->boundlessAccess )
                    {
                        $form->
                            add('update_and_return', 'submit', [
                                'label' => "Сохранить и вернуться к списку"
                            ]);
                    }
                } else {
                    $form
                        ->add('create', 'submit', [
                            'label' => "Создать"
                        ])
                    ;

                    if( $this->boundlessAccess )
                    {
                        $form
                            ->add('create_and_return', 'submit', [
                                'label' => "Создать и вернуться к списку"
                            ]);
                    }
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