<?php
// AppBundle/Form/Type/StudentType.php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\Form\FormEvent,
    Symfony\Component\Form\FormEvents,
    Symfony\Component\OptionsResolver\OptionsResolver;

class StudentType extends AbstractType
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
            ->add('surname', 'text', [
                'label' => "Surname"
            ])
            ->add('patronymic', 'text', [
                'label' => "Patronymic"
            ])
            ->add('gender', 'choice', [
                'label' => "Gender",
                'choices'         => array_combine(
                    ['male', 'female'], ["Male", "Female"]
                ),
                'expanded'        => TRUE,
                'multiple'        => FALSE,
                'invalid_message' => "Bad choice"
            ])
            ->add('dateOfBirth', 'date', [
                'label' => "Date of birth"
            ])
            ->add('school', 'entity', [
                'required'     => TRUE,
                'class'        => "AppBundle\\Entity\\School\\School",
                'choice_label' => "name",
                'placeholder'  => "Choose school"
            ])
        ;

        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event)
            {
                $student = $event->getData();

                $form = $event->getForm();

                if( $student && $student->getId() !== NULL )
                {
                    $form
                        ->add('update', 'submit', [
                            'label' => "Сохранить"
                        ])
                        ->add('totalLimit', 'number', [
                            'label' => "Total limit",
                            'scale' => 2
                        ])
                        ->add('dailyLimit', 'number', [
                            'label' => "Daily limit",
                            'scale' => 2
                        ])
                    ;

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
            'data_class'         => 'AppBundle\Entity\Student\Student',
            'translation_domain' => 'forms'
        ]);
    }

    public function getName()
    {
        return 'student';
    }
}