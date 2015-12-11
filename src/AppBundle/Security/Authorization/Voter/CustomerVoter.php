<?php
// AppBundle/Security/Authorization/Voter/CustomerVoter.php
namespace AppBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\User\UserInterface;

use AppBundle\Security\Authorization\Voter\Utility\Extended\ExtendedAbstractVoter,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface;

class CustomerVoter extends ExtendedAbstractVoter implements UserRoleListInterface
{
    const CUSTOMER_READ   = 'customer_read';
    const CUSTOMER_UPDATE = 'customer_update';
    const CUSTOMER_DELETE = 'customer_delete';
    const CUSTOMER_BIND   = 'customer_bind';

    protected function getSupportedAttributes()
    {
        return [
            self::CUSTOMER_READ,
            self::CUSTOMER_UPDATE,
            self::CUSTOMER_DELETE,
            self::CUSTOMER_BIND
        ];
    }

    protected function getSupportedClasses()
    {
        return ['AppBundle\Entity\Customer\Customer'];
    }

    protected function isGranted($attribute, $customer, $user = NULL)
    {
        if( !$user instanceof UserInterface )
            return FALSE;

        switch($attribute)
        {
            case self::CUSTOMER_READ:
                return $this->read($user);
            break;

            case self::CUSTOMER_UPDATE:
                return $this->update($customer, $user);
            break;

            case self::CUSTOMER_DELETE:
                return $this->delete($customer, $user);
            break;

            case self::CUSTOMER_BIND:
                return $this->bind($customer, $user);
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

    protected function update($customer, $user = NULL)
    {
        if( $this->hasRole($user, self::ROLE_ADMIN) )
            return TRUE;

        if( $this->hasRole($user, self::ROLE_REGISTRAR) )
        {
            if( $customer->getEmployee() )  {
                return ($customer->getEmployee()->getId() == $user->getId())
                    ? TRUE
                    : FALSE;
            }
        }

        return FALSE;
    }

    protected function delete($customer, $user = NULL)
    {
        if( $this->hasRole($user, self::ROLE_ADMIN) )
            return TRUE;

        if( $this->hasRole($user, self::ROLE_REGISTRAR) )
        {
            if( $customer->getEmployee() ) {
                return ($customer->getEmployee()->getId() == $user->getId())
                    ? TRUE
                    : FALSE;
            }
        }

        return FALSE;
    }

    protected function bind($customer, $user = NULL)
    {
        if( $this->hasRole($user, self::ROLE_ADMIN) )
            return TRUE;

        if( $this->hasRole($user, self::ROLE_REGISTRAR) )
        {
            if( $customer->getEmployee() ) {
                return ($customer->getEmployee()->getId() == $user->getId())
                    ? TRUE
                    : FALSE;
            }
        }

        return FALSE;
    }
}