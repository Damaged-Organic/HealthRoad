<?php
// AppBundle/Entity/Student/Repository/StudentRepository.php
namespace AppBundle\Entity\Student\Repository;

use AppBundle\Entity\Utility\Extended\ExtendedEntityRepository;

class StudentRepository extends ExtendedEntityRepository
{
    public function rawUpdateStudentsTotalLimits(array $studentsArray)
    {
        $queryStringWhen = $queryStringIds = '';
        $queryArgs = [];

        foreach( $studentsArray as $student )
        {
            $queryStringWhen .= " WHEN ? THEN ? ";
            $queryStringIds  .= "?,";

            $queryArgs = array_merge($queryArgs, [
                $student['id'],
                $student['totalLimit'],
                $student['id']
            ]);
        }

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
