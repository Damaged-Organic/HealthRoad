<?php
// AppBundle/Entity/Utility/Extended/ExtendedEntityRepository.php
namespace AppBundle\Entity\Utility\Extended;

use Exception;

use Doctrine\ORM\EntityRepository;

use AppBundle\Entity\Utility\Traits\DoctrinePaginator\PaginatorTrait;

class ExtendedEntityRepository extends EntityRepository
{
    use PaginatorTrait;

    protected $chain;

    public function baseChainFindBy(array $findBy, $entityAlias)
    {
        if( !$this->chain )
            throw new Exception('Chain is not defined');

        $prefix = function($key){
            return "find_by_{$key}";
        };

        foreach($findBy as $key => $value)
        {
            $this->chain
                ->andWhere("{$entityAlias}.{$key} = :" . $prefix($key))
                ->setParameter($prefix($key), $value)
            ;
        }

        return $this;
    }

    public function baseChainSearchBy($searchBy, $entityFields)
    {
        if( !$this->chain )
            throw new Exception('Chain is not defined');

        $searchString     = [];
        $searchParameters = [];

        $prefixAndFix = function($value)
        {
            $value = str_replace('.', '_', $value);

            return "search_by_{$value}";
        };

        foreach($entityFields as $value)
        {
            $searchString[] = "{$value} LIKE :" . $prefixAndFix($value);

            $this->chain->setParameter($prefixAndFix($value), "%{$searchBy}%");
        }

        $this->chain->andWhere(implode(' OR ', $searchString));

        return $this;
    }

    public function chainResultSlice($offset, $limit)
    {
        if( !$this->chain )
            throw new Exception('Chain is not defined');

        $query = $this->chain->getQuery();

        $this->chain = NULL;

        return $this->paginate($query, $offset, $limit);
    }

    public function chainResult()
    {
        if( !$this->chain )
            throw new Exception('Chain is not defined');

        $query = $this->chain->getQuery();

        $this->chain = NULL;

        return $query->getResult();
    }

    public function count()
    {
        $query = $this
            ->createQueryBuilder('e')
            ->select('count(e.id)')
            ->getQuery()
        ;

        return $query->getSingleScalarResult();
    }
}
