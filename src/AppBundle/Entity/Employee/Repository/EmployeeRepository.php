<?php
// AppBundle/Entity/Employee/Repository/EmployeeRepository.php
namespace AppBundle\Entity\Employee\Repository;

use Symfony\Component\Security\Core\User\UserInterface,
    Symfony\Component\Security\Core\User\UserProviderInterface,
    Symfony\Component\Security\Core\Exception\UsernameNotFoundException,
    Symfony\Component\Security\Core\Exception\UnsupportedUserException;

use Doctrine\ORM\NoResultException;

use AppBundle\Entity\Utility\Extended\ExtendedEntityRepository;

class EmployeeRepository extends ExtendedEntityRepository implements UserProviderInterface
{
    // BEGIN: Extended find methods
    public function findChained()
    {
        $this->chain = $this->createQueryBuilder('e')
            ->select('e, eg')
            ->leftJoin('e.employeeGroup', 'eg')
        ;

        return $this;
    }

    public function chainFindBy(array $findBy)
    {
        $this->baseChainFindBy($findBy, 'e');

        return $this;
    }

    public function chainSearchBy($searchBy)
    {
        $entityFields = [
            'e.username', 'e.name', 'e.surname', 'e.patronymic', 'e.email', 'e.phoneNumber',
            'eg.name',
        ];

        $this->baseChainSearchBy($searchBy, $entityFields);

        return $this;
    }
    // END: Extended find methods

    public function loadUserByUsername($username)
    {
        $query = $this
            ->createQueryBuilder('e')
            ->select('e, eg')
            ->leftJoin('e.employeeGroup', 'eg')
            ->where('e.username = :username')
            ->setParameter('username', $username)
            ->getQuery()
        ;

        try {
            $employee = $query->getSingleResult();
        } catch (NoResultException $EX) {
            throw new UsernameNotFoundException(
                "No single result for AppBundle:Employee\\Employee identified by `username`: \"{$username}\"", 0, $EX
            );
        }

        return $employee;
    }

    public function refreshUser(UserInterface $employee)
    {
        $class = get_class($employee);

        if( !$this->supportsClass($class) ) {
            throw new UnsupportedUserException("Instances of \"{$class}\" are not supported");
        }

        return $this->loadUserByUsername($employee->getUsername());
    }

    public function supportsClass($class)
    {
        return $this->getEntityName() === $class || is_subclass_of($class, $this->getEntityName());
    }
}
