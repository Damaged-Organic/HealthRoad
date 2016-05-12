<?php
// src/AppBundle/Entity/Utility/Traits/DoctrinePaginator/PaginatorTrait.php
namespace AppBundle\Entity\Utility\Traits\DoctrinePaginator;

use Doctrine\ORM\Tools\Pagination\Paginator;

trait PaginatorTrait
{
    private function paginate($query, $offset, $limit)
    {
        $paginator = new Paginator($query);

        $paginator
            ->getQuery()
            ->setFirstResult($offset)
            ->setMaxResults($limit)
        ;

        return $paginator;
    }
}
