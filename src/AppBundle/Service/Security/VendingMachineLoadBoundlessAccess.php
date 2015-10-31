<?php
// AppBundle/Service/Security/VendingMachineLoadBoundlessAccess.php
namespace AppBundle\Service\Security;

use AppBundle\Service\Security\Utility\Abstracted\AbstractBoundlessAccess,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface;

class VendingMachineLoadBoundlessAccess extends AbstractBoundlessAccess implements UserRoleListInterface
{
    const VENDING_MACHINE_LOAD_READ = 'vending_machine_load_read';

    public function isGranted($attribute)
    {
        switch($attribute)
        {
            case self::VENDING_MACHINE_LOAD_READ:
                return $this->_authorizationChecker->isGranted(self::ROLE_EMPLOYEE);
            break;

            default:
                return FALSE;
            break;
        }
    }
}