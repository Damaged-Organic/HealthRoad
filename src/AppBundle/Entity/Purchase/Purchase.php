<?php
// src/AppBundle/Entity/Purchase/Purchase.php
namespace AppBundle\Entity\Purchase;

use Doctrine\ORM\Mapping as ORM;

use AppBundle\Entity\Utility\Traits\DoctrineMapping\IdMapperTrait;

/**
 * @ORM\Table(name="purchases")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Purchase\Repository\PurchaseRepository")
 */
class Purchase
{
    use IdMapperTrait;

    protected $vendingMachine;

    protected $product;

    protected $nfcTag;

    protected $purchasedAt;

    protected $productPrice;

    protected $purchaseIdSync;

    protected $productPriceSync;
}