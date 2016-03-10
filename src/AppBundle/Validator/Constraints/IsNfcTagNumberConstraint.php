<?php
// src/AppBundle/Validator/Constraints/IsNfcTagNumberConstraint.php
namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class IsNfcTagNumberConstraint extends Constraint
{
    public $message = "custom.nfc_tag_number.valid";
}
