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
                'label' => "Number"
            ])
            ->add('code', 'text', [
                'label' => "Code"
            ])
            ->add('vendingMachine', 'entity', [
                'required'     => FALSE,
                'class'        => "AppBundle\\Entity\\VendingMachine\\VendingMachine",
                'choice_label' => "code",
                'placeholder'  => "Choose vending machine"
            ])
        ;

        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event)
            {
                $nfcTag = $event->getData();

                $form = $event->getForm();

                if( $nfcTag && $nfcTag->getId() !== NULL )
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
            'data_class'         => 'AppBundle\Entity\NfcTag\NfcTag',
            'translation_domain' => 'forms'
        ]);
    }

    public function getName()
    {
        return 'nfc_tag';
    }
}