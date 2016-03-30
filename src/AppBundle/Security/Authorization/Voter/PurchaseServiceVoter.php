<?php
// src/AppBundle/Security/Authorization/Voter/PurchaseServiceVoter.php
namespace AppBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\User\UserInterface;

use AppBundle\Security\Authorization\Voter\Utility\Extended\ExtendedAbstractVoter,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface;

class PurchaseServiceVoter extends ExtendedAbstractVoter implements UserRoleListInterface
{
    const PURCHASE_SERVICE_READ = "purchase_service_read";

    protected function getSupportedAttributes()
    {
        return [
            self::PURCHASE_SERVICE_READ
        ];
    }

    protected function getSupportedClasses()
    {
        return ['AppBundle\Entity\PurchaseService\PurchaseService'];
    }

    protected function isGranted($attribute, $purchaseService, $user = NULL)
    {
        if (!$user instanceof UserInterface)
            return FALSE;

        switch($attribute)
        {
            case self::PURCHASE_SERVICE_READ:
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
