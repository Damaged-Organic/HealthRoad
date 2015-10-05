<?php
// AppBundle/Service/Security/SettlementBoundlessAccess.php
namespace AppBundle\Service\Security;

use AppBundle\Service\Security\Utility\Abstracted\AbstractBoundlessAccess,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface;

class SettlementBoundlessAccess extends AbstractBoundlessAccess implements UserRoleListInterface
{
    const SETTLEMENT_READ   = 'settlement_read';
    const SETTLEMENT_CREATE = 'settlement_create';
    const SETTLEMENT_BIND   = 'settlement_bind';

    public function isGranted($attribute)
    {
        switch($attribute)
        {
            case self::SETTLEMENT_READ:
                return $this->_authorizationChecker->isGranted(self::ROLE_EMPLOYEE);
            break;

            case self::SETTLEMENT_CREATE:
                return $this->_authorizationChecker->isGranted(self::ROLE_ADMIN);
            break;

            case self::SETTLEMENT_BIND:
                return $this->_authorizationChecker->isGranted(self::ROLE_ADMIN);
            break;

            default:
                return FALSE;
            break;
        }
    }
}