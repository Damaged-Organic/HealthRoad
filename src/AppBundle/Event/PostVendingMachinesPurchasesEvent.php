<?php
// src/AppBundle/Event/PostVendingMachinesPurchasesEvent.php
namespace AppBundle\Event;

use Symfony\Component\EventDispatcher\Event;

use AppBundle\Entity\VendingMachine\VendingMachine;

class PostVendingMachinesPurchasesEvent extends Event
{
    protected $vendingMachine;
    protected $vendingMachineSyncId;

    public function __construct(VendingMachine $vendingMachine, $vendingMachineSyncId)
    {
        $this->vendingMachine       = $vendingMachine;
        $this->vendingMachineSyncId = $vendingMachineSyncId;
    }

    public function getVendingMachine()
    {
        return $this->vendingMachine;
    }

    public function getVendingMachineSyncId()
    {
        return $this->vendingMachineSyncId;
    }
}
