<?php
// src/AppBundle/Tests/Controller/SyncData/Utility/Interfaces/SyncDataTestInterface.php
namespace AppBundle\Tests\Controller\SyncData\Utility\Interfaces;

interface SyncDataTestInterface
{
    const SYNC_ID = 3;

    static public function getData();

    static public function getSyncAction();

    static public function getSyncMethod();
}
