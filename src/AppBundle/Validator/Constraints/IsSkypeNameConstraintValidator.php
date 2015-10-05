<?php
// AppBundle/Validator/Constraints/IsSkypeNameConstraintValidator.php
namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint,
    Symfony\Component\Validator\ConstraintValidator;

class IsSkypeNameConstraintValidator extends ConstraintValidator
{
    const SKYPE_NAME_PATTERN = "#[a-z][a-z0-9\\.,\\-_@]{5,31}#i";

    public function validate($value, Constraint $constraint)
    {
        if( $value && !preg_match(self::SKYPE_NAME_PATTERN, $value) )
            $this->context->buildViolation($constraint->message)
                ->addViolation();
    }
}