<?php
// AppBundle/Form/Type/Payment/StudentBalanceType.php
namespace AppBundle\Form\Type\Payment;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class StudentBalanceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('replenishLimit', 'number', [
                'mapped'   => FALSE,
                'required' => FALSE,
                'scale'    => 2,
                'label'    => 'student.replenish_limit.label',
                'attr'     => [
                    'placeholder' => 'student.replenish_limit.placeholder'
                ]
            ])
            ->add('totalLimit', 'number', [
                'required' => FALSE,
                'scale'    => 2,
                'label'    => 'student.total_limit.label',
                'attr'     => [
                    'placeholder' => 'student.total_limit.placeholder'
                ]
            ])
        ;

        $builder
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event)
            {
                $student = $event->getData();

                $form = $event->getForm();

                if( isset($student['replenishLimit']) )
                {
                    unset($student['totalLimit']);

                    $form->remove('totalLimit');
                    $event->setData($student);
                }
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => 'AppBundle\Entity\Student\Student',
            'translation_domain' => 'forms'
        ]);
    }

    public function getName()
    {
        return 'student_balance';
    }
}