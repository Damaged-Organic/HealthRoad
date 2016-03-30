<?php
// AppBundle/Form/Type/NfcTagType.php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\Form\FormEvent,
    Symfony\Component\Form\FormEvents,
    Symfony\Component\OptionsResolver\OptionsResolver;

class NfcTagType extends AbstractType
{
    private $boundlessAccess;

    public function __construct($boundlessAccess)
    {
        $this->boundlessAccess = $boundlessAccess;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('number', 'text', [
                'label' => 'nfc_tag.number.label',
                'attr'  => [
                    'placeholder' => 'nfc_tag.number.placeholder'
                ]
            ])
            ->add('code', 'text', [
                'label' => 'nfc_tag.code.label',
                'attr'  => [
                    'placeholder' => 'nfc_tag.code.placeholder'
                ]
            ])
        ;

        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event)
            {
                $nfcTag = $event->getData();

                $form = $event->getForm();

                if( $nfcTag && $nfcTag->getId() !== NULL )
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
            'data_class'         => 'AppBundle\Entity\NfcTag\NfcTag',
            'translation_domain' => 'forms'
        ]);
    }

    public function getName()
    {
        return 'nfc_tag';
    }
}
