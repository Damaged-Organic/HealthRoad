<?php
// src/AppBundle/Form/Type/CustomerNotificationSettingType.php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerNotificationSettingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('smsOnSync', 'checkbox', [
                'label' => 'customer_notification_setting.sms_on_sync.label'
            ])
            ->add('smsOnDayEnd', 'checkbox', [
                'label' => 'customer_notification_setting.sms_on_day_end.label'
            ])
            ->add('emailOnSync', 'checkbox', [
                'label' => 'customer_notification_setting.email_on_sync.label'
            ])
            ->add('emailOnDayEnd', 'checkbox', [
                'label' => 'customer_notification_setting.email_on_day_end.label'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => 'AppBundle\Entity\Customer\CustomerNotificationSetting',
            'translation_domain' => 'forms'
        ]);
    }

    public function getName()
    {
        return 'customer_notification_setting';
    }
}
