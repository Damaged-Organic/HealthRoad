<?php
// AppBundle/Security/Authorization/Voter/EmployeeVoter.php
namespace AppBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\User\UserInterface;

use AppBundle\Security\Authorization\Voter\Utility\Extended\ExtendedAbstractVoter,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface;

class EmployeeVoter extends ExtendedAbstractVoter implements UserRoleListInterface
{
    const EMPLOYEE_CREATE = 'employee_create';
    const EMPLOYEE_READ   = 'employee_read';
    const EMPLOYEE_UPDATE = 'employee_update';
    const EMPLOYEE_DELETE = 'employee_delete';

    const EMPLOYEE_BIND_REGION = 'employee_bind_region';
    const EMPLOYEE_BIND_SCHOOL = 'employee_bind_school';

    const EMPLOYEE_UPDATE_SYSTEM = 'employee_update_system';

    protected function getSupportedAttributes()
    {
        return [
            self::EMPLOYEE_CREATE,
            self::EMPLOYEE_READ,
            self::EMPLOYEE_UPDATE,
            self::EMPLOYEE_DELETE,
            self::EMPLOYEE_BIND_REGION,
            self::EMPLOYEE_BIND_SCHOOL,
            self::EMPLOYEE_UPDATE_SYSTEM
        ];
    }

    protected function getSupportedClasses()
    {
        return ['AppBundle\Entity\Employee\Employee'];
    }

    protected function isGranted($attribute, $employee, $user = NULL)
    {
        if( !$user instanceof UserInterface )
            return FALSE;

        switch($attribute)
        {
            case self::EMPLOYEE_CREATE:
                return $this->create($employee, $user);
            break;

            case self::EMPLOYEE_READ:
                return $this->read($employee, $user);
            break;

            case self::EMPLOYEE_UPDATE:
                return $this->update($employee, $user);
            break;

            case self::EMPLOYEE_DELETE:
                return $this->delete($employee, $user);
            break;

            case self::EMPLOYEE_BIND_REGION:
                return $this->bindRegion($employee, $user);
            break;

            case self::EMPLOYEE_BIND_SCHOOL:
                return $this->bindSchool($employee, $user);
            break;

            case self::EMPLOYEE_UPDATE_SYSTEM:
                return $this->updateSystem($employee, $user);
            break;

            default:
                return FALSE;
            break;
        }
    }

    protected function create($employee, $user)
    {
        if( $this->hasRole($employee, self::ROLE_SUPERADMIN) )
            return FALSE;

        if( $this->hasRole($employee, self::ROLE_ADMIN) )
        {
            return ( $this->hasRole($user, self::ROLE_SUPERADMIN) )
                ? TRUE
                : FALSE;
        }

        if( $this->hasRole($user, self::ROLE_SUPERADMIN) ||
            $this->hasRole($user, self::ROLE_ADMIN) )
            return TRUE;

        return FALSE;
    }

    protected function read($employee, $user)
    {
        if( $this->hasRole($user, self::ROLE_ADMIN) )
            return TRUE;

        if( $employee->getId() == $user->getId() )
            return TRUE;

        return FALSE;
    }

    protected function update($employee, $user = NULL)
    {
        if( $this->hasRole($employee, self::ROLE_SUPERADMIN) ) {
            return ( $employee->getId() == $user->getId() )
                ? TRUE
                : FALSE;
        }

        if( $this->hasRole($user, self::ROLE_SUPERADMIN) )
            return TRUE;

        if( $this->hasRole($employee, self::ROLE_ADMIN) ) {
            return ( $employee->getId() == $user->getId() )
                ? TRUE
                : FALSE;
        }

        if( $this->hasRole($user, self::ROLE_ADMIN) )
            return TRUE;

        if( $employee->getId() == $user->getId() )
            return TRUE;

        return FALSE;
    }

    protected function delete($employee, $user = NULL)
    {
        if( $this->hasRole($employee, self::ROLE_SUPERADMIN) )
            return FALSE;

        if( $this->hasRole($employee, self::ROLE_ADMIN) )
        {
            return ( $this->hasRole($user, self::ROLE_SUPERADMIN) )
                ? TRUE
                : FALSE;
        }

        if( $this->hasRole($user, self::ROLE_ADMIN) )
            return TRUE;

        return FALSE;
    }

    protected function bindRegion($employee, $user)
    {
        if( $this->hasRole($user, self::ROLE_ADMIN) )
        {
            return ( $employee->getRoles()[0]->getRole() === self::ROLE_MANAGER )
                ? TRUE
                : FALSE;
        }

        return FALSE;
    }

    protected function bindSchool($employee, $user)
    {
        if( $this->hasRole($user, self::ROLE_ADMIN) )
        {
            return ( $employee->getRoles()[0]->getRole() === self::ROLE_REGISTRAR )
                ? TRUE
                : FALSE;
        }

        return FALSE;
    }

    protected function updateSystem($employee, $user)
    {
        if( $this->hasRole($user, self::ROLE_SUPERADMIN) )
        {
            return ( !$this->hasRole($employee, self::ROLE_SUPERADMIN) )
                ? TRUE
                : FALSE;
        }

        if( $this->hasRole($user, self::ROLE_ADMIN) )
        {
            return ( !$this->hasRole($employee, self::ROLE_ADMIN) )
                ? TRUE
                : FALSE;
        }

        return FALSE;
    }
}