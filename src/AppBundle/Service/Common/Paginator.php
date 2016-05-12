<?php
// src/AppBundle/Service/Common/Paginator.php
namespace AppBundle\Service\Common;

use Symfony\Component\HttpFoundation\RequestStack;

use Doctrine\ORM\PersistentCollection,
    Doctrine\ORM\Tools\Pagination;

use AppBundle\Service\Common\Utility\Exceptions\PaginatorException;

use AppBundle\Entity\Utility\Extended\ExtendedEntityRepository,
    AppBundle\Service\Common\Utility\Interfaces\PaginatorInterface;

class Paginator implements PaginatorInterface
{
    private $_requestStack;

    private $recordsNumber;

    public function setRequestStack(RequestStack $requestStack)
    {
        $this->_requestStack = $requestStack;
    }

    public function setRecordsNumber($recordsNumber)
    {
        $this->recordsNumber = $recordsNumber;
    }

    public function getPageArgument()
    {
        $request = $this->_requestStack->getMasterRequest();

        if( !$request->query->has(self::PAGE_ARGUMENT) ) {
            $currentPage = $this->getFirstPage();
        } else {
            $currentPage = $request->query->get(self::PAGE_ARGUMENT);

            if( !$this->validatePageArgument($currentPage) )
                throw new PaginatorException('Invalid page argument');
        }

        return $currentPage;
    }

    public function validatePageArgument($currentPage)
    {
        $isValid = filter_var($currentPage, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => $this->getFirstPage()]
        ]);

        return ( $isValid !== FALSE );
    }

    public function getFirstPage()
    {
        return self::PAGE_FIRST;
    }

    public function getLastPage()
    {
        if( !$this->recordsNumber )
            throw new PaginatorException("Records number is not set");

        return ceil($this->recordsNumber / self::RECORD_LIMIT);
    }

    public function getOffset($page)
    {
        return self::RECORD_LIMIT * ($page - 1);
    }

    public function getLimit()
    {
        return self::RECORD_LIMIT;
    }

    public function isPaginationRequired()
    {
        return ( $this->recordsNumber ) ? TRUE : FALSE;
    }
}
