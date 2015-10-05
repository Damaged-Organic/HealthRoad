<?php
// AppBundle/Security/Authorization/Voter/ProductVendingGroupVoter.php
namespace AppBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\User\UserInterface;

use AppBundle\Security\Authorization\Voter\Utility\Extended\ExtendedAbstractVoter,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface;

class ProductVendingGroupVoter extends ExtendedAbstractVoter implements UserRoleListInterface
{
    const PRODUCT_VENDING_GROUP_READ   = "product_vending_group_read";
    const PRODUCT_VENDING_GROUP_UPDATE = "product_vending_group_update";
    const PRODUCT_VENDING_GROUP_DELETE = "product_vending_group_delete";

    const PRODUCT_VENDING_GROUP_BIND   = "product_vending_group_bind";

    protected function getSupportedAttributes()
    {
        return [
            self::PRODUCT_VENDING_GROUP_READ,
            self::PRODUCT_VENDING_GROUP_UPDATE,
            self::PRODUCT_VENDING_GROUP_DELETE,
            self::PRODUCT_VENDING_GROUP_BIND
        ];
    }

    protected function getSupportedClasses()
    {
        return ['AppBundle\Entity\Product\ProductVendingGroup'];
    }

    protected function isGranted($attribute, $productVendingGroup, $user = NULL)
    {
        if (!$user instanceof UserInterface)
            return FALSE;

        switch($attribute)
        {
            case self::PRODUCT_VENDING_GROUP_READ:
                return $this->read($user);
            break;

            case self::PRODUCT_VENDING_GROUP_UPDATE:
                return $this->update($user);
            break;

            case self::PRODUCT_VENDING_GROUP_DELETE:
                return $this->delete($user);
            break;

            case self::PRODUCT_VENDING_GROUP_BIND:
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