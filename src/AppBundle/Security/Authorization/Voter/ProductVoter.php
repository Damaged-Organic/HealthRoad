<?php
// AppBundle/Security/Authorization/Voter/ProductVoter.php
namespace AppBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\User\UserInterface;

use AppBundle\Security\Authorization\Voter\Utility\Extended\ExtendedAbstractVoter,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface;

class ProductVoter extends ExtendedAbstractVoter implements UserRoleListInterface
{
    const PRODUCT_READ   = "product_read";
    const PRODUCT_UPDATE = "product_update";
    const PRODUCT_DELETE = "product_delete";

    const PRODUCT_BIND   = "product_bind";

    protected function getSupportedAttributes()
    {
        return [
            self::PRODUCT_READ,
            self::PRODUCT_UPDATE,
            self::PRODUCT_DELETE,
            self::PRODUCT_BIND
        ];
    }

    protected function getSupportedClasses()
    {
        return ['AppBundle\Entity\Product\Product'];
    }

    protected function isGranted($attribute, $product, $user = NULL)
    {
        if (!$user instanceof UserInterface)
            return FALSE;

        switch($attribute)
        {
            case self::PRODUCT_READ:
                return $this->read($product, $user);
            break;

            case self::PRODUCT_UPDATE:
                return $this->update($product, $user);
            break;

            case self::PRODUCT_DELETE:
                return $this->delete($user);
            break;

            case self::PRODUCT_BIND:
                return $this->bind($user);
            break;

            default:
                return FALSE;
            break;
        }
    }

    protected function read($product, $user = NULL)
    {
        if( $product->getPseudoDeleted() )
        {
            return ( $this->hasRole($user, self::ROLE_ADMIN) )
                ? TRUE
                : FALSE;
        }

        if( $this->hasRole($user, self::ROLE_EMPLOYEE) )
            return TRUE;

        return FALSE;
    }

    protected function update($product, $user = NULL)
    {
        if( $product->getPseudoDeleted() )
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