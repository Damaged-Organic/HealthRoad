<?php
// AppBundle/Validator/Constraints/IsDecimalConstraint.php
namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class IsDecimalConstraint extends Constraint
{
    public $message = "custom.decimal.valid";
}