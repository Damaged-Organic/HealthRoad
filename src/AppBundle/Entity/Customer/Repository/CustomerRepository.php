<?php
// AppBundle/Entity/Customer/Repository/CustomerRepository.php
namespace AppBundle\Entity\Customer\Repository;

use Symfony\Component\Security\Core\User\UserInterface,
    Symfony\Component\Security\Core\User\UserProviderInterface,
    Symfony\Component\Security\Core\Exception\UsernameNotFoundException,
    Symfony\Component\Security\Core\Exception\UnsupportedUserException;

use Doctrine\ORM\NoResultException;

use AppBundle\Entity\Utility\Extended\ExtendedEntityRepository;

class CustomerRepository extends ExtendedEntityRepository implements UserProviderInterface
{
    public function loadUserByUsername($phoneNumber)
    {
        $query = $this
            ->createQueryBuilder('c')
            ->where('c.phoneNumber = :phoneNumber')
            ->setParameter('phoneNumber', $phoneNumber)
            ->getQuery()
        ;

        try {
            $customer = $query->getSingleResult();
        } catch (NoResultException $EX) {
            throw new UsernameNotFoundException(
                "No single result for AppBundle:Customer\\Customer identified by `phoneNumber`: \"{$phoneNumber}\"", 0, $EX
            );
        }

        return $customer;
    }

    public function refreshUser(UserInterface $customer)
    {
        $class = get_class($customer);

        if( !$this->supportsClass($class) ) {
            throw new UnsupportedUserException("Instances of \"{$class}\" are not supported");
        }

        return $this->loadUserByUsername($customer->getPhoneNumber());
    }

    public function supportsClass($class)
    {
        return $this->getEntityName() === $class || is_subclass_of($class, $this->getEntityName());
    }
}