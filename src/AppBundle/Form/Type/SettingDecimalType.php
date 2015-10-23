<?php
// src/AppBundle/Form/Type/SettingDecimalType.php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\OptionsResolver\OptionsResolver,
    Symfony\Component\Form\FormEvents,
    Symfony\Component\Form\FormEvent;

class SettingDecimalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event)
        {
            $settingDecimal = $event->getData();

            $form = $event->getForm();

            $form
                ->add('settingValue', 'number', [
                    'required' => TRUE,
                    'scale'    => 2,
                    'label'    => $settingDecimal->getName()
                ])
            ;
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => 'AppBundle\Entity\Setting\SettingDecimal',
            'translation_domain' => 'forms'
        ]);
    }

    public function getName()
    {
        return 'setting_decimal';
    }
}