<?php
// src/AppBundle/Form/Type/SettingType.php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\OptionsResolver\OptionsResolver;

class SettingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('settingsDecimal', 'collection', [
                'type' => new SettingDecimalType
            ])
            ->add('settingsString', 'collection', [
                'type' => new SettingStringType
            ])
            ->add('update', 'submit', ['label' => 'common.update.label']);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => 'AppBundle\Entity\Setting\Setting',
            'translation_domain' => 'forms'
        ]);
    }

    public function getName()
    {
        return 'setting';
    }
}