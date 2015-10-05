<?php
// src/AppBundle/Validator/Constraints/IsPhoneNumberConstraint.php
namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class IsPhoneNumberConstraint extends Constraint
{
   public $message = "custom.phone_number.valid";
}