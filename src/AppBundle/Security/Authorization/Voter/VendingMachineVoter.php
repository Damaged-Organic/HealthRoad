<?php
// AppBundle/Security/Authorization/Voter/VendingMachineVoter.php
namespace AppBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\User\UserInterface;

use AppBundle\Security\Authorization\Voter\Utility\Extended\ExtendedAbstractVoter,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface;

class VendingMachineVoter extends ExtendedAbstractVoter implements UserRoleListInterface
{
    const VENDING_MACHINE_READ   = "vending_machine_read";
    const VENDING_MACHINE_UPDATE = "vending_machine_update";
    const VENDING_MACHINE_DELETE = "vending_machine_delete";

    const VENDING_MACHINE_BIND   = "vending_machine_bind";

    protected function getSupportedAttributes()
    {
        return [
            self::VENDING_MACHINE_READ,
            self::VENDING_MACHINE_UPDATE,
            self::VENDING_MACHINE_DELETE,
            self::VENDING_MACHINE_BIND
        ];
    }

    protected function getSupportedClasses()
    {
        return ['AppBundle\Entity\VendingMachine\VendingMachine'];
    }

    protected function isGranted($attribute, $vendingMachine, $user = NULL)
    {
        if (!$user instanceof UserInterface)
            return FALSE;

        switch($attribute)
        {
            case self::VENDING_MACHINE_READ:
                return $this->read($vendingMachine, $user);
            break;

            case self::VENDING_MACHINE_UPDATE:
                return $this->update($vendingMachine, $user);
            break;

            case self::VENDING_MACHINE_DELETE:
                return $this->delete($user);
            break;

            case self::VENDING_MACHINE_BIND:
                return $this->bind($user);
            break;

            default:
                return FALSE;
            break;
        }
    }

    protected function read($vendingMachine, $user = NULL)
    {
        if( $vendingMachine->getPseudoDeleted() )
        {
            return ( $this->hasRole($user, self::ROLE_ADMIN) )
                ? TRUE
                : FALSE;
        }

        if( $this->hasRole($user, self::ROLE_EMPLOYEE) )
            return TRUE;

        return FALSE;
    }

    protected function update($vendingMachine, $user = NULL)
    {
        if( $vendingMachine->getPseudoDeleted() )
            return FALSE;

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