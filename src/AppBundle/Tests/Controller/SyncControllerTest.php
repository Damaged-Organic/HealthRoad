<?php
// src/AppBundle/Tests/Controller/SyncControllerTest.php
namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Goutte\Client;

use AppBundle\Tests\Controller\SyncData\Transaction,
    AppBundle\Tests\Controller\SyncData\Purchase,
    AppBundle\Tests\Controller\SyncData\VendingMachineEvent,
    AppBundle\Tests\Controller\SyncData\VendingMachineLoad;

class SyncControllerTest extends WebTestCase
{
    const SYNC_URL_VENDING_MACHINES = 'http://sync-v1.cheers-development.in.ua/vending_machines';

    const SYNC_METHOD_VENDING_MACHINE_LOADS = 'PUT';

    private $vendingMachineCredentials = [
        'serial'   => 'tstboard-0001',
        'login'    => 'xxx_login',
        'password' => 'xxx_password'
    ];

    private function getSyncMethodVendingMachineLoads()
    {
        return self::SYNC_METHOD_VENDING_MACHINE_LOADS;
    }

    private function getVendingMachineSerial()
    {
        return $this->vendingMachineCredentials['serial'];
    }

    private function getVendingMachineLogin()
    {
        return $this->vendingMachineCredentials['login'];
    }

    private function getVendingMachinePassword()
    {
        return $this->vendingMachineCredentials['password'];
    }

    private function getVendingMachineAuthentificationString()
    {
        return "login={$this->getVendingMachineLogin()}&password={$this->getVendingMachinePassword()}";
    }

    private function getVendingMachineConnectionUrl($vendingMachineConnectionPath)
    {
        return self::SYNC_URL_VENDING_MACHINES . '/' . $vendingMachineConnectionPath;
    }

    /**
     * @group purchases
     */
    public function testSyncPurchases()
    {
        $vendingMachineConnectionPath = sprintf(
            "%s/%s?%s",
            $this->getVendingMachineSerial(),
            Purchase::getSyncAction(),
            $this->getVendingMachineAuthentificationString()
        );

        $vendingMachineConnectionUrl = $this->getVendingMachineConnectionUrl($vendingMachineConnectionPath);

        $client = new Client();
        $client->request(
            Purchase::getSyncMethod(),
            $vendingMachineConnectionUrl,
            [], [], ['CONTENT_TYPE' => 'application/json'], Purchase::getData()
        );

        $this->assertEquals(200, $client->getResponse()->getStatus());
        $this->assertEquals('null', $client->getResponse()->getContent());

        $client->request(
            Purchase::getSyncMethod(),
            $vendingMachineConnectionUrl,
            [], [], ['CONTENT_TYPE' => 'application/json'], Purchase::getData()
        );

        $this->assertEquals(200, $client->getResponse()->getStatus());
        $this->assertEquals('Already in sync', $client->getResponse()->getContent());
    }

    /**
     * @group transactions
     */
    public function testSyncTransactions()
    {
        $vendingMachineConnectionPath = sprintf(
            "%s/%s?%s",
            $this->getVendingMachineSerial(),
            Transaction::getSyncAction(),
            $this->getVendingMachineAuthentificationString()
        );

        $vendingMachineConnectionUrl = $this->getVendingMachineConnectionUrl($vendingMachineConnectionPath);

        $client = new Client();
        $client->request(
            Transaction::getSyncMethod(),
            $vendingMachineConnectionUrl,
            [], [], ['CONTENT_TYPE' => 'application/json'], Transaction::getData()
        );

        $this->assertEquals(200, $client->getResponse()->getStatus());
        $this->assertEquals('null', $client->getResponse()->getContent());

        $client->request(
            Transaction::getSyncMethod(),
            $vendingMachineConnectionUrl,
            [], [], ['CONTENT_TYPE' => 'application/json'], Transaction::getData()
        );

        $this->assertEquals(200, $client->getResponse()->getStatus());
        $this->assertEquals('Already in sync', $client->getResponse()->getContent());
    }

    public function testSyncVendingMachineEvents()
    {
        $vendingMachineConnectionPath = sprintf(
            "%s/%s?%s",
            $this->getVendingMachineSerial(),
            VendingMachineEvent::getSyncAction(),
            $this->getVendingMachineAuthentificationString()
        );

        $vendingMachineConnectionUrl = $this->getVendingMachineConnectionUrl($vendingMachineConnectionPath);

        $client = new Client();
        $client->request(
            VendingMachineEvent::getSyncMethod(),
            $vendingMachineConnectionUrl,
            [], [], ['CONTENT_TYPE' => 'application/json'], VendingMachineEvent::getData()
        );

        $this->assertEquals(200, $client->getResponse()->getStatus());
        $this->assertEquals('null', $client->getResponse()->getContent());
    }

    public function testSyncVendingMachineLoads()
    {
        $vendingMachineConnectionPath = sprintf(
            "%s?%s",
            $this->getVendingMachineSerial(),
            $this->getVendingMachineAuthentificationString()
        );

        $vendingMachineConnectionUrl = $this->getVendingMachineConnectionUrl($vendingMachineConnectionPath);

        $client = new Client();
        $client->request(
            VendingMachineLoad::getSyncMethod(),
            $vendingMachineConnectionUrl,
            [], [], ['CONTENT_TYPE' => 'application/json'], VendingMachineLoad::getData()
        );

        $this->assertEquals(200, $client->getResponse()->getStatus());
        $this->assertEquals('null', $client->getResponse()->getContent());
    }
}
