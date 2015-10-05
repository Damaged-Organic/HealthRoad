<?php
// src/AppBundle/Validator/Constraints/IsPhoneNumberConstraintValidator.php
namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint,
    Symfony\Component\Validator\ConstraintValidator;

class IsPhoneNumberConstraintValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if( $value && !preg_match('/^\+38\s\(0[0-9]{2}\)\s[0-9]{3}\-[0-9]{2}\-[0-9]{2}$/', $value, $matches) ) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}