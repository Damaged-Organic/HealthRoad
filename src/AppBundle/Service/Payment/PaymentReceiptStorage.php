<?php
// src/AppBundle/Service/Payment/PaymentReceiptStorage.php
namespace AppBundle\Service\Payment;

use Symfony\Component\Security\Core\SecurityContext;

use Predis\Client as Redis,
    Predis\Collection\Iterator\Keyspace;

class PaymentReceiptStorage
{
    const REDIS_RECEIPT_KEY = 'payments_receipts';

    private $_redis;
    private $_securityContext;

    public function setRedis(Redis $redis)
    {
        $this->_redis = $redis;
    }

    public function setSecurityContext(SecurityContext $securityContext)
    {
        $this->_securityContext = $securityContext;
    }

    private function getUserId()
    {
        return $this->_securityContext->getToken()->getUser()->getId();
    }

    public function saveReceipt(array $receipt)
    {
        $this->_redis->multi();

        foreach( $receipt as $id => $entry )
        {
            $this->_redis->hmset($this->getReceiptKey($postfix = $id), $entry);
            $this->_redis->expire($this->getReceiptKey($postfix = $id), 1800);
        }

        $this->_redis->exec();
    }

    public function readReceipt()
    {
        $receipt = [];

        foreach( $this->getReceiptKeyspace() as $key )
        {
            $receipt[] = $this->_redis->hgetall($key);
        }

        return $receipt;
    }

    public function clearReceipt()
    {
        foreach( $this->getReceiptKeyspace() as $key )
        {
            $this->_redis->del($key);
        }
    }

    private function getReceiptKey($postfix = NULL)
    {
        $userId = $this->getUserId();

        return self::REDIS_RECEIPT_KEY . ":{$userId}:{$postfix}";
    }

    private function getReceiptKeyspace()
    {
        return new Keyspace($this->_redis, $this->getReceiptKey($postfix = '*'));
    }
}
