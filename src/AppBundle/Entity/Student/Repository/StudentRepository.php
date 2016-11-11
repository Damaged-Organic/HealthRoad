<?php
// AppBundle/Entity/Student/Repository/StudentRepository.php
namespace AppBundle\Entity\Student\Repository;

use AppBundle\Entity\Utility\Extended\ExtendedEntityRepository,
    AppBundle\Entity\VendingMachine\VendingMachine;

class StudentRepository extends ExtendedEntityRepository
{
    // BEGIN: Extended find methods
    public function findChained()
    {
        $this->chain = $this->createQueryBuilder('st')
            ->select('st, c, s, nt')
            ->leftJoin('st.customer', 'c')
            ->leftJoin('st.school', 's')
            ->leftJoin('st.nfcTag', 'nt')
        ;

        return $this;
    }

    public function chainFindBy(array $findBy)
    {
        $this->baseChainFindBy($findBy, 'st');

        return $this;
    }

    public function chainSearchBy($searchBy)
    {
        $entityFields = [
            'st.name', 'st.surname', 'st.patronymic',
            's.name', 's.address',
            'nt.number',
            'c.name', 'c.surname', 'c.patronymic',
        ];

        $this->baseChainSearchBy($searchBy, $entityFields);

        return $this;
    }
    // END: Extended find methods

    public function rawUpdateStudentsTotalLimits(array $studentsArray)
    {
        $queryStringWhen = $queryStringIds = '';
        $queryArgsWhen = $queryArgsIds = $queryArgs = [];

        foreach( $studentsArray as $student )
        {
            $queryStringWhen .= " WHEN ? THEN ? ";
            $queryStringIds  .= "?,";

            $queryArgsWhen = array_merge($queryArgsWhen, [
                $student['id'],
                $student['totalLimit']
            ]);

            $queryArgsIds = array_merge($queryArgsIds, [
                $student['id']
            ]);
        }

        $queryArgs = array_merge($queryArgsWhen, $queryArgsIds);

        if( !$queryArgs )
            return;

        $queryStringIds = substr($queryStringIds, 0, -1);

        $queryString = "
            UPDATE students
            SET total_limit =
            (CASE id {$queryStringWhen} END)
            WHERE id IN ({$queryStringIds})
        ";

        $stmt = $this->getEntityManager()->getConnection()->prepare($queryString);

        $stmt->execute($queryArgs);
    }
}
