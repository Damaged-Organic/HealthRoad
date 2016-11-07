<?php
// src/AppBundle/Security/Authorization/Voter/TransactionVoter.php
namespace AppBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\User\UserInterface;

use AppBundle\Security\Authorization\Voter\Utility\Extended\ExtendedAbstractVoter,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface;

class TransactionVoter extends ExtendedAbstractVoter implements UserRoleListInterface
{
    const TRANSACTION_READ = "transaction_read";

    protected function getSupportedAttributes()
    {
        return [
            self::TRANSACTION_READ
        ];
    }

    protected function getSupportedClasses()
    {
        return ['AppBundle\Entity\Transaction\Transaction'];
    }

    protected function isGranted($attribute, $transaction, $user = NULL)
    {
        if (!$user instanceof UserInterface)
            return FALSE;

        switch($attribute)
        {
            case self::TRANSACTION_READ:
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
