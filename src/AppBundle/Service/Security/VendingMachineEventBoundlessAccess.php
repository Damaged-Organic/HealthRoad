<?php
// AppBundle/Service/Security/VendingMachineEventBoundlessAccess.php
namespace AppBundle\Service\Security;

use AppBundle\Service\Security\Utility\Abstracted\AbstractBoundlessAccess,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface;

class VendingMachineEventBoundlessAccess extends AbstractBoundlessAccess implements UserRoleListInterface
{
    const VENDING_MACHINE_EVENT_READ = 'vending_machine_event_read';

    public function isGranted($attribute)
    {
        switch($attribute)
        {
            case self::VENDING_MACHINE_EVENT_READ:
                return $this->_authorizationChecker->isGranted(self::ROLE_EMPLOYEE);
            break;

            default:
                return FALSE;
            break;
        }
    }
}