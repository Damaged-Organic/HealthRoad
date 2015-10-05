<?php
// AppBundle/Form/Type/SchoolType.php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\Form\FormEvent,
    Symfony\Component\Form\FormEvents,
    Symfony\Component\OptionsResolver\OptionsResolver;

class SchoolType extends AbstractType
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
                'label' => "School name"
            ])
            ->add('settlement', 'entity', [
                'class'        => "AppBundle\\Entity\\Settlement\\Settlement",
                'choice_label' => "name",
                'placeholder'  => "Choose settlement"
            ])
            ->add('address', 'text', [
                'label' => "Address"
            ])
            ->add('studentsQuantity', 'number', [
                'required' => FALSE,
                'label'    => "Students quantity"
            ])
            ->add('phoneNumberSchool', 'text', [
                'required' => FALSE,
                'label'    => "School phone number"
            ])
            ->add('emailSchool', 'email', [
                'required' => FALSE,
                'label'    => "School email"
            ])
            ->add('nameHeadmaster', 'text', [
                'required' => FALSE,
                'label'    => "Headmaster name"
            ])
            ->add('phoneNumberHeadmaster', 'text', [
                'required' => FALSE,
                'label'    => "Headmaster phone number"
            ])
            ->add('emailHeadmaster', 'email', [
                'required' => FALSE,
                'label'    => "Headmaster email"
            ])
            ->add('nameContact', 'text', [
                'required' => FALSE,
                'label'    => "Contact name"
            ])
            ->add('phoneNumberContact', 'text', [
                'required' => FALSE,
                'label'    => "Contact phone number"
            ])
            ->add('emailContact', 'email', [
                'required' => FALSE,
                'label'    => "Contact email"
            ])
        ;

        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event)
            {
                $school = $event->getData();

                $form = $event->getForm();

                if( $school && $school->getId() !== NULL )
                {
                    $form
                        ->add('update', 'submit', [
                            'label' => "Сохранить"
                        ])
                    ;

                    if( $this->boundlessAccess )
                    {
                        $form->
                        add('update_and_return', 'submit', [
                            'label' => "Сохранить и вернуться к списку"
                        ]);
                    }
                } else {
                    $form
                        ->add('create', 'submit', [
                            'label' => "Создать"
                        ])
                    ;

                    if( $this->boundlessAccess )
                    {
                        $form
                            ->add('create_and_return', 'submit', [
                                'label' => "Создать и вернуться к списку"
                            ]);
                    }
                }
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => 'AppBundle\Entity\School\School',
            'translation_domain' => 'forms'
        ]);
    }

    public function getName()
    {
        return 'school';
    }
}