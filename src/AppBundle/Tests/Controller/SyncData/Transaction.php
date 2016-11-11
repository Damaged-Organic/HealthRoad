<?php
// src/AppBundle/Tests/Controller/SyncData/Transaction.php
namespace AppBundle\Tests\Controller\SyncData;

use DateTime;

use AppBundle\Tests\Controller\SyncData\Utility\Interfaces\SyncDataTestInterface;

class Transaction implements SyncDataTestInterface
{
    const SYNC_ACTION = 'transactions';
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
        		'transactions' => [
        			[
        				'id'                   => 1,
        				'transaction-datetime' => (new DateTime)->format('Y-m-d H:i:s'),
        				'nfc-code'             => "04c0188a584980",
        				'student-id'           => 1,
        				'banknotes'            => [
        					[
        						'currency' => 'UAH',
        						'nominal'  => '10.00',
        						'quantity' => 5
        					],
        					[
        						'currency' => 'UAH',
        						'nominal'  => '20.00',
        						'quantity' => 10
        					],
        					[
        						'currency' => 'UAH',
        						'nominal'  => '50.00',
        						'quantity' => 5
        					]
        				]
        			],
        		]
        	]
        ];

        $data['checksum'] = hash('sha256', json_encode($data['data']));

        return json_encode($data);
    }
}
