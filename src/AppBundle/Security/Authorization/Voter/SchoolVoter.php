<?php
// AppBundle/Security/Authorization/Voter/SchoolVoter.php
namespace AppBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\User\UserInterface;

use AppBundle\Security\Authorization\Voter\Utility\Extended\ExtendedAbstractVoter,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface;

class SchoolVoter extends ExtendedAbstractVoter implements UserRoleListInterface
{
    const SCHOOL_READ   = 'school_read';
    const SCHOOL_UPDATE = 'school_update';
    const SCHOOL_DELETE = 'school_delete';

    const SCHOOL_BIND   = 'school_bind';

    protected function getSupportedAttributes()
    {
        return [
            self::SCHOOL_READ,
            self::SCHOOL_UPDATE,
            self::SCHOOL_DELETE,
            self::SCHOOL_BIND
        ];
    }

    protected function getSupportedClasses()
    {
        return ['AppBundle\Entity\School\School'];
    }

    protected function isGranted($attribute, $school, $user = NULL)
    {
        if( !$user instanceof UserInterface )
            return FALSE;

        switch($attribute)
        {
            case self::SCHOOL_READ:
                return $this->read($user);
            break;

            case self::SCHOOL_UPDATE:
                return $this->update($user);
            break;

            case self::SCHOOL_DELETE:
                return $this->delete($user);
            break;

            case self::SCHOOL_BIND:
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

    protected function bind($user)
    {
        if( $this->hasRole($user, self::ROLE_ADMIN) )
            return TRUE;

        return FALSE;
    }
}