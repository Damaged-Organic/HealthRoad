<?php
// AppBundle/Service/Security/RegionBoundlessAccess.php
namespace AppBundle\Service\Security;

use AppBundle\Service\Security\Utility\Abstracted\AbstractBoundlessAccess,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface;

class RegionBoundlessAccess extends AbstractBoundlessAccess implements UserRoleListInterface
{
    const REGION_READ   = 'region_read';
    const REGION_CREATE = 'region_create';
    const REGION_BIND   = 'region_bind';

    public function isGranted($attribute)
    {
        switch($attribute)
        {
            case self::REGION_READ:
                return $this->_authorizationChecker->isGranted(self::ROLE_EMPLOYEE);
            break;

            case self::REGION_CREATE:
                return $this->_authorizationChecker->isGranted(self::ROLE_ADMIN);
            break;

            case self::REGION_BIND:
                return $this->_authorizationChecker->isGranted(self::ROLE_ADMIN);
            break;

            default:
                return FALSE;
            break;
        }
    }
}