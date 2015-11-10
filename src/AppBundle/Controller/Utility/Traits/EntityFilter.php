<?php
// AppBundle/Controller/Utility/Traits/EntityFilter.php
namespace AppBundle\Controller\Utility\Traits;

use Traversable;

trait EntityFilter
{
    public function filterDeletedIfNotGranted($permission, $inputArray = NULL)
    {
        $outputArray = [];

        if( is_array($inputArray) || ($inputArray instanceof Traversable) )
        {
            foreach ($inputArray as $object) {
                if( $this->isGranted($permission, $object) )
                    $outputArray[] = $object;
            }
        }

        return $outputArray;
    }

    public function filterDeleted($inputArray = NULL)
    {
        $outputArray = [];

        if( is_array($inputArray) || ($inputArray instanceof Traversable) )
        {
            foreach ($inputArray as $object) {
                if( !$object->getPseudoDeleted() )
                    $outputArray[] = $object;
            }
        }

        return $outputArray;
    }
}