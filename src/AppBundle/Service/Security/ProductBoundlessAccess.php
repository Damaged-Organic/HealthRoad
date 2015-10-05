<?php
// AppBundle/Service/Security/ProductBoundlessAccess.php
namespace AppBundle\Service\Security;

use AppBundle\Service\Security\Utility\Abstracted\AbstractBoundlessAccess,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface;

class ProductBoundlessAccess extends AbstractBoundlessAccess implements UserRoleListInterface
{
    const PRODUCT_READ   = 'product_read';
    const PRODUCT_CREATE = 'product_create';
    const PRODUCT_BIND   = 'product_bind';

    public function isGranted($attribute)
    {
        switch($attribute)
        {
            case self::PRODUCT_READ:
                return $this->_authorizationChecker->isGranted(self::ROLE_EMPLOYEE);
            break;

            case self::PRODUCT_CREATE:
                return $this->_authorizationChecker->isGranted(self::ROLE_ADMIN);
            break;

            case self::PRODUCT_BIND:
                return $this->_authorizationChecker->isGranted(self::ROLE_ADMIN);
            break;

            default:
                return FALSE;
            break;
        }
    }
}