<?php
// AppBundle/Entity/Employee/Repository/EmployeeGroupRepository.php
namespace AppBundle\Entity\Employee\Repository;

use AppBundle\Entity\Utility\Extended\ExtendedEntityRepository,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface;

class EmployeeGroupRepository extends ExtendedEntityRepository implements UserRoleListInterface
{
    public function getSubordinateRolesQuery($role)
    {
        $query = $this
            ->createQueryBuilder('eg')
            ->select('eg')
        ;

        switch($role)
        {
            case self::ROLE_SUPERADMIN:
                $query
                    ->where('eg.role <> :role')
                    ->setParameter('role', self::ROLE_SUPERADMIN)
                ;
            break;

            case self::ROLE_ADMIN:
                $query
                    ->where('eg.role <> :roleOne')
                    ->andWhere('eg.role <> :roleTwo')
                    ->setParameters([
                        'roleOne' => self::ROLE_SUPERADMIN,
                        'roleTwo' => self::ROLE_ADMIN,
                    ])
                ;
            break;

            default:
                return FALSE;
            break;
        }

        $query->orderBy('eg.id', 'ASC');

        return $query;
    }
}