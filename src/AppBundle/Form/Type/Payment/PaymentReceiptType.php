<?php
// src/AppBundle/Form/Type/Payment/PaymentReceiptType.php
namespace AppBundle\Form\Type\Payment;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\OptionsResolver\OptionsResolver,
    Symfony\Component\Form\FormEvent,
    Symfony\Component\Form\FormEvents;

class PaymentReceiptType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('paymentReceiptFile', 'file', [
                'label'    => 'payment_receipt.payment_receipt_file.label',
                'attr'     => [
                    'placeholder' => 'payment_receipt.payment_receipt_file.placeholder'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => 'AppBundle\Model\Payment\PaymentReceiptFile',
            'translation_domain' => 'forms'
        ]);
    }

    public function getName()
    {
        return 'payment_replenish_receipt';
    }
}
