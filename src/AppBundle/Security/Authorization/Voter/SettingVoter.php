<?php
// AppBundle/Security/Authorization/Voter/SettingVoter.php
namespace AppBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\User\UserInterface;

use AppBundle\Security\Authorization\Voter\Utility\Extended\ExtendedAbstractVoter,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface;

class SettingVoter extends ExtendedAbstractVoter implements UserRoleListInterface
{
    const SETTING_UPDATE = 'setting_update';

    protected function getSupportedAttributes()
    {
        return [
            self::SETTING_UPDATE
        ];
    }

    protected function getSupportedClasses()
    {
        return ['AppBundle\Entity\Setting\Setting'];
    }

    protected function isGranted($attribute, $setting, $user = NULL)
    {
        if( !$user instanceof UserInterface )
            return FALSE;

        switch($attribute)
        {
            case self::SETTING_UPDATE:
                return $this->update($user);
            break;

            default:
                return FALSE;
            break;
        }
    }

    public function update($user = NULL)
    {
        if( $this->hasRole($user, self::ROLE_SUPERADMIN) )
            return TRUE;

        return FALSE;
    }
}