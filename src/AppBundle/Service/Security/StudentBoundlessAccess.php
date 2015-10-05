<?php
// AppBundle/Service/Security/StudentBoundlessAccess.php
namespace AppBundle\Service\Security;

use AppBundle\Service\Security\Utility\Abstracted\AbstractBoundlessAccess,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface;

class StudentBoundlessAccess extends AbstractBoundlessAccess implements UserRoleListInterface
{
    const STUDENT_READ   = 'student_read';
    const STUDENT_CREATE = 'student_create';
    const STUDENT_BIND   = 'student_bind';

    public function isGranted($attribute)
    {
        switch($attribute)
        {
            case self::STUDENT_READ:
                return $this->_authorizationChecker->isGranted(self::ROLE_EMPLOYEE);
            break;

            case self::STUDENT_CREATE:
                return $this->_authorizationChecker->isGranted(self::ROLE_REGISTRAR);
            break;

            case self::STUDENT_BIND:
                return $this->_authorizationChecker->isGranted(self::ROLE_REGISTRAR);
            break;

            default:
                return FALSE;
            break;
        }
    }
}