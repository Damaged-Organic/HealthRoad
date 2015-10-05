<?php
// AppBundle/Security/Authorization/Voter/SettlementVoter.php
namespace AppBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\User\UserInterface;

use AppBundle\Security\Authorization\Voter\Utility\Extended\ExtendedAbstractVoter,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface;

class SettlementVoter extends ExtendedAbstractVoter implements UserRoleListInterface
{
    const SETTLEMENT_READ   = 'settlement_read';
    const SETTLEMENT_UPDATE = 'settlement_update';
    const SETTLEMENT_DELETE = 'settlement_delete';

    const SETTLEMENT_BIND   = 'settlement_bind';

    protected function getSupportedAttributes()
    {
        return [
            self::SETTLEMENT_READ,
            self::SETTLEMENT_UPDATE,
            self::SETTLEMENT_DELETE,
            self::SETTLEMENT_BIND
        ];
    }

    protected function getSupportedClasses()
    {
        return ['AppBundle\Entity\Settlement\Settlement'];
    }

    protected function isGranted($attribute, $settlement, $user = NULL)
    {
        if( !$user instanceof UserInterface )
            return FALSE;

        switch($attribute)
        {
            case self::SETTLEMENT_READ:
                return $this->read($user);
            break;

            case self::SETTLEMENT_UPDATE:
                return $this->update($user);
            break;

            case self::SETTLEMENT_DELETE:
                return $this->delete($user);
            break;

            case self::SETTLEMENT_BIND:
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