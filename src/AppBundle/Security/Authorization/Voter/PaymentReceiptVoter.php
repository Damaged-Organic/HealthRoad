<?php
// src/AppBundle/Security/Authorization/Voter/PaymentReceiptVoter.php
namespace AppBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\User\UserInterface;

use AppBundle\Security\Authorization\Voter\Utility\Extended\ExtendedAbstractVoter,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Entity\Payment\PaymentReceipt;

class PaymentReceiptVoter extends ExtendedAbstractVoter implements UserRoleListInterface
{
    const PAYMENT_RECEIPT_READ = "payment_receipt_read";

    protected function getSupportedAttributes()
    {
        return [
            self::PAYMENT_RECEIPT_READ
        ];
    }

    protected function getSupportedClasses()
    {
        return ['AppBundle\Entity\Payment\PaymentReceipt'];
    }

    protected function isGranted($attribute, $paymentReceipt, $user = NULL)
    {
        if( !$user instanceof UserInterface )
            return FALSE;

        switch($attribute)
        {
            case self::PAYMENT_RECEIPT_READ:
                return $this->read($user);
            break;

            default:
                return FALSE;
            break;
        }
    }

    protected function read($user = NULL)
    {
        if( $this->hasRole($user, self::ROLE_EMPLOYEE) )
            return TRUE;

        return FALSE;
    }
}
