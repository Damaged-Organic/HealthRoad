<?php
// AppBundle/Service/Security/VendingMachineBoundlessAccess.php
namespace AppBundle\Service\Security;

use AppBundle\Service\Security\Utility\Abstracted\AbstractBoundlessAccess,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface;

class VendingMachineBoundlessAccess extends AbstractBoundlessAccess implements UserRoleListInterface
{
    const VENDING_MACHINE_READ   = 'vending_machine_read';
    const VENDING_MACHINE_CREATE = 'vending_machine_create';
    const VENDING_MACHINE_BIND   = 'vending_machine_bind';

    public function isGranted($attribute)
    {
        switch($attribute)
        {
            case self::VENDING_MACHINE_READ:
                return $this->_authorizationChecker->isGranted(self::ROLE_EMPLOYEE);
            break;

            case self::VENDING_MACHINE_CREATE:
                return $this->_authorizationChecker->isGranted(self::ROLE_ADMIN);
            break;

            case self::VENDING_MACHINE_BIND:
                return $this->_authorizationChecker->isGranted(self::ROLE_ADMIN);
            break;

            default:
                return FALSE;
            break;
        }
    }
}