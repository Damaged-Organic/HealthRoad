<?php
// AppBundle/Validator/Constraints/IsPriceConstraint.php
namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class IsPriceConstraint extends Constraint
{
    public $message = "custom.price.valid";
}