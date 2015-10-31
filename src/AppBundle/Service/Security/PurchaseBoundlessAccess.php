<?php
// AppBundle/Service/Security/PurchaseBoundlessAccess.php
namespace AppBundle\Service\Security;

use AppBundle\Service\Security\Utility\Abstracted\AbstractBoundlessAccess,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface;

class PurchaseBoundlessAccess extends AbstractBoundlessAccess implements UserRoleListInterface
{
    const PURCHASE_READ = 'purchase_read';

    public function isGranted($attribute)
    {
        switch($attribute)
        {
            case self::PURCHASE_READ:
                return $this->_authorizationChecker->isGranted(self::ROLE_EMPLOYEE);
            break;

            default:
                return FALSE;
            break;
        }
    }
}