<?php
// AppBundle/Form/Type/StudentType.php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\Form\FormEvent,
    Symfony\Component\Form\FormEvents,
    Symfony\Component\OptionsResolver\OptionsResolver,
    Symfony\Component\Translation\TranslatorInterface;

class StudentType extends AbstractType
{
    private $_translator;

    private $boundlessAccess;

    public function __construct(TranslatorInterface $translator, $boundlessAccess)
    {
        $this->_translator = $translator;

        $this->boundlessAccess = $boundlessAccess;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', [
                'label' => 'student.name.label',
                'attr'  => [
                    'placeholder' => 'student.name.placeholder'
                ]
            ])
            ->add('surname', 'text', [
                'label' => 'student.surname.label',
                'attr'  => [
                    'placeholder' => 'student.surname.placeholder'
                ]
            ])
            ->add('patronymic', 'text', [
                'label' => 'student.patronymic.label',
                'attr'  => [
                    'placeholder' => 'student.patronymic.placeholder'
                ]
            ])
            ->add('gender', 'choice', [
                'label'   => "Gender",
                'choices' => array_combine(
                    ['male', 'female'], ['student.gender.choice.male', 'student.gender.choice.female']
                ),
                'expanded'        => TRUE,
                'multiple'        => FALSE,
                'invalid_message' => $this->_translator->trans('student.gender.choice', [], 'validators')
            ])
            ->add('dateOfBirth', 'date', [
                'widget' => 'single_text',
                'format' => 'dd-MM-yy',
                'label'  => 'student.date_of_birth.label',
                'attr'   => [
                    'placeholder' => 'student.date_of_birth.placeholder'
                ]
            ])
            ->add('school', 'entity', [
                'required'     => TRUE,
                'class'        => "AppBundle\\Entity\\School\\School",
                'choice_label' => "name",
                'label'        => 'student.school.label'
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
                        ->add('totalLimit', 'number', [
                            'scale' => 2,
                            'label' => 'student.total_limit.label',
                            'attr'  => [
                                'placeholder' => 'student.total_limit.placeholder'
                            ]
                        ])
                        ->add('dailyLimit', 'number', [
                            'scale' => 2,
                            'label' => 'student.daily_limit.label',
                            'attr'  => [
                                'placeholder' => 'student.daily_limit.placeholder'
                            ]
                        ])
                        ->add('update', 'submit', ['label' => 'common.update.label'])
                    ;

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
            'data_class'         => 'AppBundle\Entity\Student\Student',
            'translation_domain' => 'forms'
        ]);
    }

    public function getName()
    {
        return 'student';
    }
}