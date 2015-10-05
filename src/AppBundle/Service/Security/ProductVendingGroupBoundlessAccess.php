<?php
// AppBundle/Service/Security/ProductVendingGroupBoundlessAccess.php
namespace AppBundle\Service\Security;

use AppBundle\Service\Security\Utility\Abstracted\AbstractBoundlessAccess,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface;

class ProductVendingGroupBoundlessAccess extends AbstractBoundlessAccess implements UserRoleListInterface
{
    const PRODUCT_VENDING_GROUP_READ   = 'product_vending_group_read';
    const PRODUCT_VENDING_GROUP_CREATE = 'product_vending_group_create';
    const PRODUCT_VENDING_GROUP_BIND   = 'product_vending_group_bind';

    public function isGranted($attribute)
    {
        switch($attribute)
        {
            case self::PRODUCT_VENDING_GROUP_READ:
                return $this->_authorizationChecker->isGranted(self::ROLE_EMPLOYEE);
            break;

            case self::PRODUCT_VENDING_GROUP_CREATE:
                return $this->_authorizationChecker->isGranted(self::ROLE_ADMIN);
            break;

            case self::PRODUCT_VENDING_GROUP_BIND:
                return $this->_authorizationChecker->isGranted(self::ROLE_ADMIN);
            break;

            default:
                return FALSE;
            break;
        }
    }
}