<?php
// AppBundle/Entity/Student/Repository/StudentRepository.php
namespace AppBundle\Entity\Student\Repository;

use AppBundle\Entity\Utility\Extended\ExtendedEntityRepository;

class StudentRepository extends ExtendedEntityRepository
{
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
