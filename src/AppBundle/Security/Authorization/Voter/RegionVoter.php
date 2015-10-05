<?php
// AppBundle/Security/Authorization/Voter/RegionVoter.php
namespace AppBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\User\UserInterface;

use AppBundle\Security\Authorization\Voter\Utility\Extended\ExtendedAbstractVoter,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface;

class RegionVoter extends ExtendedAbstractVoter implements UserRoleListInterface
{
    const REGION_READ   = 'region_read';
    const REGION_UPDATE = 'region_update';
    const REGION_DELETE = 'region_delete';

    const REGION_BIND   = 'region_bind';

    protected function getSupportedAttributes()
    {
        return [
            self::REGION_READ,
            self::REGION_UPDATE,
            self::REGION_DELETE,
            self::REGION_BIND
        ];
    }

    protected function getSupportedClasses()
    {
        return ['AppBundle\Entity\Region\Region'];
    }

    protected function isGranted($attribute, $region, $user = NULL)
    {
        if( !$user instanceof UserInterface )
            return FALSE;

        switch($attribute)
        {
            case self::REGION_READ:
                return $this->read($user);
            break;

            case self::REGION_UPDATE:
                return $this->update($user);
            break;

            case self::REGION_DELETE:
                return $this->delete($user);
            break;

            case self::REGION_BIND:
                return $this->bind($user);
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

    protected function update($user = NULL)
    {
        if( $this->hasRole($user, self::ROLE_ADMIN) )
            return TRUE;

        return FALSE;
    }

    protected function delete($user = NULL)
    {
        if( $this->hasRole($user, self::ROLE_ADMIN) )
            return TRUE;

        return FALSE;
    }

    protected function bind($user = NULL)
    {
        if( $this->hasRole($user, self::ROLE_ADMIN) )
            return TRUE;

        return FALSE;
    }
}