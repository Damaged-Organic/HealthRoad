<?php
// src/AppBundle/Security/Authorization/Voter/BanknoteListVoter.php
namespace AppBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\User\UserInterface;

use AppBundle\Security\Authorization\Voter\Utility\Extended\ExtendedAbstractVoter,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface;

class BanknoteListVoter extends ExtendedAbstractVoter implements UserRoleListInterface
{
    const BANKNOTE_LIST_READ = "banknote_list_read";

    protected function getSupportedAttributes()
    {
        return [
            self::BANKNOTE_LIST_READ
        ];
    }

    protected function getSupportedClasses()
    {
        return ['AppBundle\Entity\Banknote\BanknoteList'];
    }

    protected function isGranted($attribute, $banknoteList, $user = NULL)
    {
        if (!$user instanceof UserInterface)
            return FALSE;

        switch($attribute)
        {
            case self::BANKNOTE_LIST_READ:
                return $this->read($user);
            break;

            default:
                return FALSE;
            break;
        }
    }

    protected function read($user = NULL)
    {
        if( $this->hasRole($user, self::ROLE_EMPLOYEE) )
            return TRUE;

        return FALSE;
    }
}
