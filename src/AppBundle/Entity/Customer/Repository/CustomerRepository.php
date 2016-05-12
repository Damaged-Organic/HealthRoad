<?php
// AppBundle/Entity/Customer/Repository/CustomerRepository.php
namespace AppBundle\Entity\Customer\Repository;

use DateTime;

use Symfony\Component\Security\Core\User\UserInterface,
    Symfony\Component\Security\Core\User\UserProviderInterface,
    Symfony\Component\Security\Core\Exception\UsernameNotFoundException,
    Symfony\Component\Security\Core\Exception\UnsupportedUserException;

use Doctrine\ORM\NoResultException;

use AppBundle\Entity\Utility\Extended\ExtendedEntityRepository,
    AppBundle\Entity\VendingMachine\VendingMachine;

class CustomerRepository extends ExtendedEntityRepository implements UserProviderInterface
{
    // BEGIN: Extended find methods
    public function findChained()
    {
        $this->chain = $this->createQueryBuilder('c')
            ->select('c')
        ;

        return $this;
    }

    public function chainFindBy(array $findBy)
    {
        $this->baseChainFindBy($findBy, 'c');

        return $this;
    }

    public function chainSearchBy($searchBy)
    {
        $entityFields = [
            'c.phoneNumber', 'c.name', 'c.surname', 'c.patronymic',
        ];

        $this->baseChainSearchBy($searchBy, $entityFields);

        return $this;
    }
    // END: Extended find methods

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

    public function findPurchasesOnSync(VendingMachine $vendingMachine, $syncId)
    {
        $query = $this->createQueryBuilder('c')
            ->select('c, cns, s, p, pr')
            ->leftJoin('c.customerNotificationSetting', 'cns')
            ->leftJoin('c.students', 's')
            ->leftJoin('s.purchases', 'p')
            ->leftJoin('p.product', 'pr')
            ->where('p.vendingMachine = :vendingMachine')
            ->andWhere('p.vendingMachineSyncId = :syncId')
            ->setParameters([
                'vendingMachine' => $vendingMachine,
                'syncId'         => $syncId
            ])
            ->getQuery()
        ;

        return $query->getResult();
    }

    public function findPurchasesOnDayEnd()
    {
        $today = (new DateTime)->format('Y-m-d');

        $query = $this->createQueryBuilder('c')
            ->select('c, cns, s, p, pr')
            ->leftJoin('c.customerNotificationSetting', 'cns')
            ->leftJoin('c.students', 's')
            ->leftJoin('s.purchases', 'p')
            ->leftJoin('p.product', 'pr')
            ->where('cns.smsOnDayEnd = :smsOnDayEnd OR cns.emailOnDayEnd = :emailOnDayEnd')
            ->andWhere('p.syncPurchasedAt >= :dateStart')
            ->andWhere('p.syncPurchasedAt < :dateEnd')
            ->setParameters([
                'smsOnDayEnd'   => TRUE,
                'emailOnDayEnd' => TRUE,
                'dateStart'     => "{$today} 00:00:00",
                'dateEnd'       => "{$today} 23:59:59"
            ])
            ->getQuery()
        ;

        return $query->getResult();
    }
}
