<?php
// src/AppBundle/Tests/Controller/SyncData/VendingMachineEvent.php
namespace AppBundle\Tests\Controller\SyncData;

use DateTime;

use AppBundle\Tests\Controller\SyncData\Utility\Interfaces\SyncDataTestInterface;

class VendingMachineEvent implements SyncDataTestInterface
{
    const SYNC_ACTION = 'events';
    const SYNC_METHOD = 'POST';

    static public function getSyncAction()
    {
        return self::SYNC_ACTION;
    }

    static public function getSyncMethod()
    {
        return self::SYNC_METHOD;
    }

    static public function getData()
    {
        $data = [
            'data' => [
                'events' => [
                    [
                        'id'             => 1,
                        'event-datetime' => (new DateTime)->format('Y-m-d H:i:s'),
                        'type'           => 'some_type',
                        'code'           => '0',
                        'message'        => "some_message"
                    ],
                    [
                        'id'             => 2,
                        'event-datetime' => (new DateTime)->format('Y-m-d H:i:s'),
                        'type'           => 'some_type',
                        'code'           => '101',
                        'message'        => "some_message"
                    ]
                ]
            ]
        ];

        $data['checksum'] = hash('sha256', json_encode($data['data']));

        return json_encode($data);
    }
}
