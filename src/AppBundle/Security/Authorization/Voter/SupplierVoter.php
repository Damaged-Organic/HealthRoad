<?php
// AppBundle/Security/Authorization/Voter/SupplierVoter.php
namespace AppBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\User\UserInterface;

use AppBundle\Security\Authorization\Voter\Utility\Extended\ExtendedAbstractVoter,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface;

class SupplierVoter extends ExtendedAbstractVoter implements UserRoleListInterface
{
    const SUPPLIER_READ   = "supplier_read";
    const SUPPLIER_UPDATE = "supplier_update";
    const SUPPLIER_DELETE = "supplier_delete";

    const SUPPLIER_BIND   = "supplier_bind";

    protected function getSupportedAttributes()
    {
        return [
            self::SUPPLIER_READ,
            self::SUPPLIER_UPDATE,
            self::SUPPLIER_DELETE,
            self::SUPPLIER_BIND
        ];
    }

    protected function getSupportedClasses()
    {
        return ['AppBundle\Entity\Supplier\Supplier'];
    }

    protected function isGranted($attribute, $supplier, $user = NULL)
    {
        if (!$user instanceof UserInterface)
            return FALSE;

        switch($attribute)
        {
            case self::SUPPLIER_READ:
                return $this->read($user);
            break;

            case self::SUPPLIER_UPDATE:
                return $this->update($user);
            break;

            case self::SUPPLIER_DELETE:
                return $this->delete($user);
            break;

            case self::SUPPLIER_BIND:
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