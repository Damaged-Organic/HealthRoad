<?php
// src/AppBundle/Tests/Controller/SyncData/Purchase.php
namespace AppBundle\Tests\Controller\SyncData;

use DateTime;

use AppBundle\Tests\Controller\SyncData\Utility\Interfaces\SyncDataTestInterface;

class Purchase implements SyncDataTestInterface
{
    const SYNC_ACTION = 'purchases';
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
                'sync' => [
                    [
                        'id' => hash('sha256', self::SYNC_ID)
                    ]
                ],
                'purchases' => [
                    [
                        'id'                => 1,
                        'purchase-datetime' => (new DateTime)->format('Y-m-d H:i:s'),
                        'student-id'        => 3,
                        'nfc-code'          => "5826fcb36bb8a",
                        'product-id'        => 1,
                        'product-price'     => '42.00',
                    ],
        			[
                        'id'                => 1,
                        'purchase-datetime' => (new DateTime)->format('Y-m-d H:i:s'),
                        'student-id'        => 3,
                        'nfc-code'          => "5826fcb36bb8a",
                        'product-id'        => 2,
                        'product-price'     => '42.00',
                    ]
                ]
            ]
        ];

        $data['checksum'] = hash('sha256', json_encode($data['data']));

        return json_encode($data);
    }
}
