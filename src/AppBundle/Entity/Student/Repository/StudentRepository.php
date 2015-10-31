<?php
// AppBundle/Entity/Student/Repository/StudentRepository.php
namespace AppBundle\Entity\Student\Repository;

use AppBundle\Entity\Utility\Extended\ExtendedEntityRepository;

class StudentRepository extends ExtendedEntityRepository
{
    public function rawUpdateStudentsTotalLimits(array $studentsArray)
    {
        $queryStringWhen = $queryStringIds = '';

        foreach( $studentsArray as $student )
        {
            $queryStringWhen .= " WHEN {$student['id']} THEN '{$student['totalLimit']}' ";
            $queryStringIds  .= "{$student['id']},";
        }

        $queryStringIds = substr($queryStringIds, 0, -1);

        $queryString = "
            UPDATE students
            SET total_limit =
            (CASE id {$queryStringWhen} END)
            WHERE id IN ({$queryStringIds})
        ";

        $stmt = $this->getEntityManager()->getConnection()->prepare($queryString);

        $stmt->execute();
    }
}