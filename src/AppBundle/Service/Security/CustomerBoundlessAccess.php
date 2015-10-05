<?php
// AppBundle/Service/Security/CustomerBoundlessAccess.php
namespace AppBundle\Service\Security;

use AppBundle\Service\Security\Utility\Abstracted\AbstractBoundlessAccess,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface;

class CustomerBoundlessAccess extends AbstractBoundlessAccess implements UserRoleListInterface
{
    const CUSTOMER_READ   = 'customer_read';
    const CUSTOMER_CREATE = 'customer_create';
    const CUSTOMER_BIND   = 'customer_bind';

    public function isGranted($attribute)
    {
        switch($attribute)
        {
            case self::CUSTOMER_READ:
                return $this->_authorizationChecker->isGranted(self::ROLE_EMPLOYEE);
            break;

            case self::CUSTOMER_CREATE:
                return $this->_authorizationChecker->isGranted(self::ROLE_REGISTRAR);
            break;

            case self::CUSTOMER_BIND:
                return $this->_authorizationChecker->isGranted(self::ROLE_REGISTRAR);
            break;

            default:
                return FALSE;
            break;
        }
    }
}