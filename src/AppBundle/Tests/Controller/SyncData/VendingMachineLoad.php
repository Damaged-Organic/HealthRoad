<?php
// src/AppBundle/Tests/Controller/SyncData/VendingMachineLoad.php
namespace AppBundle\Tests\Controller\SyncData;

use DateTime;

use AppBundle\Tests\Controller\SyncData\Utility\Interfaces\SyncDataTestInterface;

class VendingMachineLoad implements SyncDataTestInterface
{
    const SYNC_METHOD = 'PUT';

    static public function getSyncAction()
    {
        return NULL;
    }

    static public function getSyncMethod()
    {
        return self::SYNC_METHOD;
    }

    static public function getData()
    {
        $data = [
            'data' => [
                'vending-machine' => [
                    [
                        'load-datetime' => (new DateTime)->format('Y-m-d H:i:s')
                    ]
                ],
        		'fill' => [
        			[
        				'position'  => '1',
        				'date-time' => (new DateTime)->format('Y-m-d H:i:s'),
        				'id'        => '1',
        				'count'     => '10'
        			],
        			[
        				'position'  => '2',
        				'date-time' => (new DateTime)->format('Y-m-d H:i:s'),
        				'id'        => '2',
        				'count'     => '15'
        			],
        			[
        				'position'  => '3',
        				'date-time' => (new DateTime)->format('Y-m-d H:i:s'),
        				'id'        => '3',
        				'count'     => '20'
        			]
        		]
            ]
        ];

        $data['checksum'] = hash('sha256', json_encode($data['data']));

        return json_encode($data);
    }
}
