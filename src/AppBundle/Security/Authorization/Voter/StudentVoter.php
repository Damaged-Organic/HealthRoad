<?php
// AppBundle/Security/Authorization/Voter/StudentVoter.php
namespace AppBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\User\UserInterface;

use AppBundle\Security\Authorization\Voter\Utility\Extended\ExtendedAbstractVoter,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface;

class StudentVoter extends ExtendedAbstractVoter implements UserRoleListInterface
{
    const STUDENT_READ   = 'student_read';
    const STUDENT_UPDATE = 'student_update';
    const STUDENT_DELETE = 'student_delete';
    const STUDENT_BIND   = 'student_bind';

    const STUDENT_BALANCE_REPLENISH = "student_balance_replenish";

    protected function getSupportedAttributes()
    {
        return [
            self::STUDENT_READ,
            self::STUDENT_UPDATE,
            self::STUDENT_DELETE,
            self::STUDENT_BIND,
            self::STUDENT_BALANCE_REPLENISH
        ];
    }

    protected function getSupportedClasses()
    {
        return ['AppBundle\Entity\Student\Student'];
    }

    protected function isGranted($attribute, $student, $user = NULL)
    {
        if( !$user instanceof UserInterface )
            return FALSE;

        switch($attribute)
        {
            case self::STUDENT_READ:
                return $this->read($user);
            break;

            case self::STUDENT_UPDATE:
                return $this->update($student, $user);
            break;

            case self::STUDENT_DELETE:
                return $this->delete($student, $user);
            break;

            case self::STUDENT_BIND:
                return $this->bind($student, $user);
            break;

            case self::STUDENT_BALANCE_REPLENISH:
                return $this->balanceReplenish($student, $user);
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

    protected function update($student, $user = NULL)
    {
        if( $this->hasRole($user, self::ROLE_ADMIN) )
            return TRUE;

        if( $this->hasRole($user, self::ROLE_REGISTRAR) )
        {
            if( $student->getEmployee() ) {
                return ($student->getEmployee()->getId() == $user->getId())
                    ? TRUE
                    : FALSE;
            }
        }

        return FALSE;
    }

    protected function delete($student, $user = NULL)
    {
        if( $this->hasRole($user, self::ROLE_ADMIN) )
            return TRUE;

        if( $this->hasRole($user, self::ROLE_REGISTRAR) ) {
            return ( $student->getEmployee()->getId() == $user->getId() )
                ? TRUE
                : FALSE;
        }

        return FALSE;
    }

    protected function bind($student, $user = NULL)
    {
        if( $this->hasRole($user, self::ROLE_ADMIN) )
            return TRUE;

        if( $this->hasRole($user, self::ROLE_REGISTRAR) ) {
            return ( $student->getEmployee()->getId() == $user->getId() )
                ? TRUE
                : FALSE;
        }

        return FALSE;
    }

    protected function balanceReplenish($student, $user = NULL)
    {
        if( $user->getRoles()[0]->getRole() === self::ROLE_ACCOUNTANT )
            return TRUE;

        return FALSE;
    }
}