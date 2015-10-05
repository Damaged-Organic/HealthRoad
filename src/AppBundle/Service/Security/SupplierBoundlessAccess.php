<?php
// AppBundle/Service/Security/SupplierBoundlessAccess.php
namespace AppBundle\Service\Security;

use AppBundle\Service\Security\Utility\Abstracted\AbstractBoundlessAccess,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface;

class SupplierBoundlessAccess extends AbstractBoundlessAccess implements UserRoleListInterface
{
    const SUPPLIER_READ   = 'supplier_read';
    const SUPPLIER_CREATE = 'supplier_create';
    const SUPPLIER_BIND   = 'supplier_bind';

    public function isGranted($attribute)
    {
        switch($attribute)
        {
            case self::SUPPLIER_READ:
                return $this->_authorizationChecker->isGranted(self::ROLE_EMPLOYEE);
            break;

            case self::SUPPLIER_CREATE:
                return $this->_authorizationChecker->isGranted(self::ROLE_ADMIN);
            break;

            case self::SUPPLIER_BIND:
                return $this->_authorizationChecker->isGranted(self::ROLE_ADMIN);
            break;

            default:
                return FALSE;
            break;
        }
    }
}