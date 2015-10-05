<?php
// AppBundle/Service/Security/SettingBoundlessAccess.php
namespace AppBundle\Service\Security;

use AppBundle\Service\Security\Utility\Abstracted\AbstractBoundlessAccess,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface;

class SettingBoundlessAccess extends AbstractBoundlessAccess implements UserRoleListInterface
{
    const SETTING_READ = 'setting_read';

    public function isGranted($attribute)
    {
        switch($attribute)
        {
            case self::SETTING_READ:
                return $this->_authorizationChecker->isGranted(self::ROLE_EMPLOYEE);
            break;

            default:
                return FALSE;
            break;
        }
    }
}