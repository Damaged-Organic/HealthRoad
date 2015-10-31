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
                'label' => 'settlement.name.label',
                'attr'  => [
                    'placeholder' => 'settlement.name.placeholder'
                ]
            ])
            ->add('region', 'entity', [
                'required'     => FALSE,
                'class'        => "AppBundle\\Entity\\Region\\Region",
                'choice_label' => "name",
                'label'        => 'settlement.region.label',
                'empty_value'  => 'common.choice.placeholder',
            ])
        ;

        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event)
            {
                $settlement = $event->getData();

                $form = $event->getForm();

                if( $settlement && $settlement->getId() !== NULL )
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
            'data_class'         => 'AppBundle\Entity\Settlement\Settlement',
            'translation_domain' => 'forms'
        ]);
    }

    public function getName()
    {
        return 'settlement';
    }
}