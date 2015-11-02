<?php
// AppBundle/Controller/Utility/Traits/FormErrorsTrait.php
namespace AppBundle\Controller\Utility\Traits;

use Symfony\Component\Form\Form;

trait FormErrorsTrait
{
    private function getFormErrorMessages(Form $form)
    {
        $errors = [];

        foreach( $form->getErrors() as $key => $error )
        {
            if( $form->isRoot() ) {
                $errors['#root'][] = $error->getMessage();
            } else {
                $errors[] = $error->getMessage();
            }
        }

        foreach( $form->all() as $child )
        {
            if( !$child->isValid() )
                $errors[$child->getName()] = $this->getFormErrorMessages($child);
        }

        return $errors;
    }
}