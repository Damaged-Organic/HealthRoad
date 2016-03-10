<?php
// src/AppBundle/Validator/Constraints/IsNfcTagNumberConstraintValidator.php
namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint,
    Symfony\Component\Validator\ConstraintValidator;

class IsNfcTagNumberConstraintValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if( $value && !preg_match('/^[A-Z]{2}[0-9]{6}$/', $value, $matches) ) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
