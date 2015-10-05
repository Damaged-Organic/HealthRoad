<?php
// AppBundle/Service/Security/NfcTagBoundlessAccess.php
namespace AppBundle\Service\Security;

use AppBundle\Service\Security\Utility\Abstracted\AbstractBoundlessAccess,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface;

class NfcTagBoundlessAccess extends AbstractBoundlessAccess implements UserRoleListInterface
{
    const NFC_TAG_READ   = 'student_read';
    const NFC_TAG_CREATE = 'student_create';
    const NFC_TAG_BIND   = 'student_bind';

    public function isGranted($attribute)
    {
        switch($attribute)
        {
            case self::NFC_TAG_READ:
                return $this->_authorizationChecker->isGranted(self::ROLE_EMPLOYEE);
            break;

            case self::NFC_TAG_CREATE:
                return $this->_authorizationChecker->isGranted(self::ROLE_ADMIN);
            break;

            case self::NFC_TAG_BIND:
                return $this->_authorizationChecker->isGranted(self::ROLE_ADMIN);
            break;

            default:
                return FALSE;
            break;
        }
    }
}