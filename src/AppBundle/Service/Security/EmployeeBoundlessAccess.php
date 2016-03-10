<?php
// AppBundle/Service/Security/EmployeeBoundlessAccess.php
namespace AppBundle\Service\Security;

use AppBundle\Service\Security\Utility\Abstracted\AbstractBoundlessAccess,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface;

class EmployeeBoundlessAccess extends AbstractBoundlessAccess implements UserRoleListInterface
{
    const EMPLOYEE_READ   = 'employee_read';
    const EMPLOYEE_CREATE = 'employee_create';

    public function isGranted($attribute)
    {
        switch($attribute)
        {
            case self::EMPLOYEE_READ:
                return $this->_authorizationChecker->isGranted(self::ROLE_ADMIN);
            break;

            /*
             * TRICKY: returns a string containing exact user role,
             * (which also equals TRUE during loose (==) authorization check)
             * and is made because isGranted() without object to vote on
             * is not providing user role hierarchy
             */
            case self::EMPLOYEE_CREATE:
                if( $this->_authorizationChecker->isGranted(self::ROLE_SUPERADMIN) )
                    return self::ROLE_SUPERADMIN;

                if( $this->_authorizationChecker->isGranted(self::ROLE_ADMIN) )
                    return self::ROLE_ADMIN;

                return FALSE;
            break;

            default:
                return FALSE;
            break;
        }
    }
}
