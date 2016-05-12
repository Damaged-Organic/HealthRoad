<?php
// src/AppBundle/Service/Common/Search.php
namespace AppBundle\Service\Common;

use Symfony\Component\HttpFoundation\RequestStack;

use AppBundle\Service\Common\Utility\Exceptions\SearchException;

use AppBundle\Service\Common\Utility\Interfaces\SearchInterface;

class Search implements SearchInterface
{
    private $_requestStack;

    public function setRequestStack(RequestStack $requestStack)
    {
        $this->_requestStack = $requestStack;
    }

    public function getSearchArgument()
    {
        $request = $this->_requestStack->getMasterRequest();

        if( !$request->query->has(self::SEARCH_ARGUMENT) ) {
            $searchArgument = NULL;
        } else {
            if( empty($request->query->get(self::SEARCH_ARGUMENT)) )
                throw new SearchException('Search argument is empty');

            $searchArgument = filter_var(
                $request->query->get(self::SEARCH_ARGUMENT),
                FILTER_SANITIZE_STRING
            );
        }

        return $searchArgument;
    }

    public function getAnySearchArgument()
    {
        $request = $this->_requestStack->getMasterRequest();

        if( !$request->query->has(self::SEARCH_ARGUMENT) ) {
            $searchArgument = NULL;
        } else {
            $searchArgument = filter_var(
                $request->query->get(self::SEARCH_ARGUMENT),
                FILTER_SANITIZE_STRING
            );
        }

        return $searchArgument;
    }

    public function searchCollectionCallback($item, $searchArgument)
    {
        $searchArgument = mb_strtolower($searchArgument, 'UTF-8');

        foreach($item->getSearchProperties() as $property)
        {
            $property = mb_strtolower($property, 'UTF-8');

            if( strpos($property, $searchArgument) !== FALSE )
                return TRUE;
        }

        return FALSE;
    }
}
