<?php
// AppBundle/Form/Type/SettlementType.php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\Form\FormEvent,
    Symfony\Component\Form\FormEvents,
    Symfony\Component\OptionsResolver\OptionsResolver;

class SettlementType extends AbstractType
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
            ->add('region', 'entity', [
                'class'        => "AppBundle\\Entity\\Region\\Region",
                'choice_label' => "name",
                'placeholder'  => "Choose region"
            ])
        ;

        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event)
            {
                $settlement = $event->getData();

                $form = $event->getForm();

                if( $settlement && $settlement->getId() !== NULL )
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
            'data_class'         => 'AppBundle\Entity\Settlement\Settlement',
            'translation_domain' => 'forms'
        ]);
    }

    public function getName()
    {
        return 'settlement';
    }
}