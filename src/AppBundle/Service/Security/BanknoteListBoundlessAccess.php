<?php
// src/AppBundle/Service/Security/BanknoteListBoundlessAccess.php
namespace AppBundle\Service\Security;

use AppBundle\Service\Security\Utility\Abstracted\AbstractBoundlessAccess,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface;

class BanknoteListBoundlessAccess extends AbstractBoundlessAccess implements UserRoleListInterface
{
    const BANKNOTE_LIST_READ = 'banknote_list_read';

    public function isGranted($attribute)
    {
        switch($attribute)
        {
            case self::BANKNOTE_LIST_READ:
                return $this->_authorizationChecker->isGranted(self::ROLE_EMPLOYEE);
            break;

            default:
                return FALSE;
            break;
        }
    }
}
