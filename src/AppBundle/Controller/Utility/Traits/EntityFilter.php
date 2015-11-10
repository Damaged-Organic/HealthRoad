<?php
// AppBundle/Controller/Utility/Traits/EntityFilter.php
namespace AppBundle\Controller\Utility\Traits;

trait EntityFilter
{
    public function filterDeletedIfNotGranted($permission, $inputArray = NULL)
    {
        $outputArray = [];

        if( is_array($inputArray) )
        {
            foreach ($inputArray as $object) {
                if ($this->isGranted($permission, $object))
                    $outputArray[] = $object;
            }
        }

        return $outputArray;
    }
}