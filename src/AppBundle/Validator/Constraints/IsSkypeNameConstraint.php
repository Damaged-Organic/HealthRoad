<?php
// AppBundle/Validator/Constraints/IsSkypeNameConstraint.php
namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class IsSkypeNameConstraint extends Constraint
{
    public $message = "custom.skype_name.valid";
}