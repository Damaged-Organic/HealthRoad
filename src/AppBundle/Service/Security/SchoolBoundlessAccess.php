<?php
// AppBundle/Service/Security/SchoolBoundlessAccess.php
namespace AppBundle\Service\Security;

use AppBundle\Service\Security\Utility\Abstracted\AbstractBoundlessAccess,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface;

class SchoolBoundlessAccess extends AbstractBoundlessAccess implements UserRoleListInterface
{
    const SCHOOL_READ   = 'school_read';
    const SCHOOL_CREATE = 'school_create';
    const SCHOOL_BIND   = 'school_bind';

    public function isGranted($attribute)
    {
        switch($attribute)
        {
            case self::SCHOOL_READ:
                return $this->_authorizationChecker->isGranted(self::ROLE_EMPLOYEE);
            break;

            case self::SCHOOL_CREATE:
                return $this->_authorizationChecker->isGranted(self::ROLE_ADMIN);
            break;

            case self::SCHOOL_BIND:
                return $this->_authorizationChecker->isGranted(self::ROLE_ADMIN);
            break;

            default:
                return FALSE;
            break;
        }
    }
}