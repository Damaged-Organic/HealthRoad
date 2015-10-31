<?php
// AppBundle/Security/Authorization/Voter/PurchaseVoter.php
namespace AppBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\User\UserInterface;

use AppBundle\Security\Authorization\Voter\Utility\Extended\ExtendedAbstractVoter,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface;

class PurchaseVoter extends ExtendedAbstractVoter implements UserRoleListInterface
{
    const PURCHASE_READ = "purchase_read";

    protected function getSupportedAttributes()
    {
        return [
            self::PURCHASE_READ
        ];
    }

    protected function getSupportedClasses()
    {
        return ['AppBundle\Entity\Purchase\Purchase'];
    }

    protected function isGranted($attribute, $purchase, $user = NULL)
    {
        if (!$user instanceof UserInterface)
            return FALSE;

        switch($attribute)
        {
            case self::PURCHASE_READ:
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