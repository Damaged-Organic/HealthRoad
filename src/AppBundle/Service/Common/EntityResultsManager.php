<?php
// src/AppBundle/Service/Common/EntityResultsManager.php
namespace AppBundle\Service\Common;

use Exception;

use Symfony\Component\HttpFoundation\RequestStack,
    Symfony\Bundle\FrameworkBundle\Routing\Router;

use Doctrine\ORM\PersistentCollection;

use AppBundle\Entity\Utility\Extended\ExtendedEntityRepository,
    AppBundle\Service\Common\Utility\Interfaces\PaginatorInterface,
    AppBundle\Service\Common\Utility\Interfaces\SearchInterface,
    AppBundle\Service\Common\Paginator;

class EntityResultsManager implements PaginatorInterface, SearchInterface
{
    private $_requestStack;
    private $_router;
    private $_paginator;
    private $_search;

    private $pageArgument;
    private $searchArgument;
    private $findArgument;

    private $routeArguments;

    public function setRequestStack(RequestStack $requestStack)
    {
        $this->_requestStack = $requestStack;
    }

    public function setRouter(Router $router)
    {
        $this->_router = $router;
    }

    public function setPaginator(Paginator $paginator)
    {
        $this->_paginator = $paginator;
    }

    public function setSearch(Search $search)
    {
        $this->_search = $search;
    }

    public function setPageArgument($pageArgument)
    {
        $this->pageArgument = $pageArgument;

        return $this;
    }

    public function setSearchArgument($searchArgument)
    {
        $this->searchArgument = $searchArgument;

        return $this;
    }

    public function setFindArgument($findArgument)
    {
        $this->findArgument = $findArgument;

        return $this;
    }

    public function setRouteArguments($arguments = [])
    {
        $this->routeArguments = $arguments;
    }

    public function findRecords($object)
    {
        switch(TRUE)
        {
            case $object instanceof ExtendedEntityRepository:
                return $this->findRecordsFromRepository($object);
            break;

            case $object instanceof PersistentCollection:
                return $this->findRecordsFromCollection($object);
            break;

            default:
                throw new Exception('Unsupported object type');
            break;
        }
    }

    private function findRecordsFromRepository(ExtendedEntityRepository $repository)
    {
        $repository->findChained();

        if( !empty($this->findArgument) ) {
            $repository->chainFindBy($this->findArgument);
        }

        if( !empty($this->searchArgument) ) {
            $repository->chainSearchBy($this->searchArgument);
        }

        if( !empty($this->pageArgument) ) {
            $records = $repository->chainResultSlice(
                $this->_paginator->getOffset($this->pageArgument),
                $this->_paginator->getLimit()
            );
        } else {
            $records = $repository->chainResult();
        }

        if( !$this->recordsFromRepositoryExist($records) )
            return FALSE;

        $this->_paginator->setRecordsNumber($records->count());

        return $records;
    }

    private function findRecordsFromCollection(PersistentCollection $collection)
    {
        if( !empty($this->searchArgument) )
            $collection = $collection->filter(function($item) {
                return $this->_search->searchCollectionCallback(
                    $item, $this->searchArgument
                );
            });

        /**
         * Searching collection could give zero results, which then will be
         * evaluated as FALSE during records existance check. I suppose that if
         * collection got records, but paginated result gives 0 records - means
         * user requested page out of available range and 404 should rise.
         *
         * So here I set collection after search as an initial.
         */
        $records = $collection;

        if( !empty($this->pageArgument) ) {
            $records = $records->slice(
                $this->_paginator->getOffset($this->pageArgument),
                $this->_paginator->getLimit()
            );
        } else {
            $records = $records;
        }

        if( !$this->recordsFromCollectionExist($records, $collection) )
            return FALSE;

        $this->_paginator->setRecordsNumber($collection->count());

        return $records;
    }

    private function recordsFromRepositoryExist($records)
    {
        if ( $records->count() && !$records->getIterator()->count() )
            return FALSE;

        return TRUE;
    }

    private function recordsFromCollectionExist($records, $collection)
    {
        if( $collection->count() && !count($records) )
            return FALSE;

        return TRUE;
    }

    public function getLink($page)
    {
        if( !$this->_paginator->validatePageArgument($page) )
            throw new \Exception("Invalid page link");

        $route = $this->_requestStack->getMasterRequest()->get('_route');

        $linkArguments = $this->getLinkArguments($page);

        if( $this->routeArguments ) {
            $routeArguments = array_merge($linkArguments, $this->routeArguments);
        } else {
            $routeArguments = $linkArguments;
        }

        return $this->_router->generate($route, $routeArguments);
    }

    private function getLinkArguments($page = NULL)
    {
        $linkArguments = [];

        if( $page )
            $linkArguments = array_merge(
                $linkArguments, [self::PAGE_ARGUMENT => $page]
            );

        if( $this->searchArgument )
            $linkArguments = array_merge(
                $linkArguments, [self::SEARCH_ARGUMENT => $this->searchArgument]
            );

        return $linkArguments;
    }
}
