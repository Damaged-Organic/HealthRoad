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
                'label' => 'school.name.label',
                'attr'  => [
                    'placeholder' => 'school.name.placeholder'
                ]
            ])
            ->add('settlement', 'entity', [
                'required'     => FALSE,
                'class'        => "AppBundle\\Entity\\Settlement\\Settlement",
                'choice_label' => "name",
                'label'        => 'school.settlement.label',
                'empty_value'  => 'common.choice.placeholder'
            ])
            ->add('address', 'text', [
                'label' => 'school.address.label',
                'attr'  => [
                    'placeholder' => 'school.address.placeholder'
                ]
            ])
            ->add('studentsQuantity', 'number', [
                'required' => FALSE,
                'label'    => 'school.students_quantity.label',
                'attr'     => [
                    'placeholder' => 'school.students_quantity.placeholder'
                ]
            ])
            ->add('phoneNumberSchool', 'text', [
                'required' => FALSE,
                'label'    => 'school.phone_number_school.label',
                'attr'     => [
                    'placeholder' => 'school.phone_number_school.placeholder'
                ]
            ])
            ->add('emailSchool', 'email', [
                'required' => FALSE,
                'label'    => 'school.email_school.label',
                'attr'     => [
                    'placeholder' => 'school.email_school.placeholder'
                ]
            ])
            ->add('nameHeadmaster', 'text', [
                'required' => FALSE,
                'label'    => 'school.name_headmaster.label',
                'attr'     => [
                    'placeholder' => 'school.name_headmaster.placeholder'
                ]
            ])
            ->add('phoneNumberHeadmaster', 'text', [
                'required' => FALSE,
                'label'    => 'school.phone_number_headmaster.label',
                'attr'     => [
                    'placeholder' => 'school.phone_number_headmaster.placeholder'
                ]
            ])
            ->add('emailHeadmaster', 'email', [
                'required' => FALSE,
                'label'    => 'school.email_headmaster.label',
                'attr'     => [
                    'placeholder' => 'school.email_headmaster.placeholder'
                ]
            ])
            ->add('nameContact', 'text', [
                'required' => FALSE,
                'label'    => 'school.name_contact.label',
                'attr'     => [
                    'placeholder' => 'school.name_contact.placeholder'
                ]
            ])
            ->add('phoneNumberContact', 'text', [
                'required' => FALSE,
                'label'    => 'school.phone_number_contact.label',
                'attr'     => [
                    'placeholder' => 'school.phone_number_contact.placeholder'
                ]
            ])
            ->add('emailContact', 'email', [
                'required' => FALSE,
                'label'    => 'school.email_contact.label',
                'attr'     => [
                    'placeholder' => 'school.email_contact.placeholder'
                ]
            ])
        ;

        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event)
            {
                $school = $event->getData();

                $form = $event->getForm();

                if( $school && $school->getId() !== NULL )
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
            'data_class'         => 'AppBundle\Entity\School\School',
            'translation_domain' => 'forms'
        ]);
    }

    public function getName()
    {
        return 'school';
    }
}