<?php
// src/AppBundle/Service/Security/PurchaseServiceBoundlessAccess.php
namespace AppBundle\Service\Security;

use AppBundle\Service\Security\Utility\Abstracted\AbstractBoundlessAccess,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface;

class PurchaseServiceBoundlessAccess extends AbstractBoundlessAccess implements UserRoleListInterface
{
    const PURCHASE_SERVICE_READ = 'purchase_service_read';

    public function isGranted($attribute)
    {
        switch($attribute)
        {
            case self::PURCHASE_SERVICE_READ:
                return $this->_authorizationChecker->isGranted(self::ROLE_EMPLOYEE);
            break;

            default:
                return FALSE;
            break;
        }
    }
}
