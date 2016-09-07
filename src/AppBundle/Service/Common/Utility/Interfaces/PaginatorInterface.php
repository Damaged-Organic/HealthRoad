<?php
// src/AppBundle/Service/Common/Utility/Interfaces/PaginatorInterface.php
namespace AppBundle\Service\Common\Utility\Interfaces;

interface PaginatorInterface
{
    const RECORD_LIMIT = 200;

    const PAGE_FIRST    = 1;
    const PAGE_ARGUMENT = 'page';

    const PAGES_RANGE = 5;
}
