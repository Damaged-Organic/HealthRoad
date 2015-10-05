<?php
// AppBundle/Entity/VendingMachine/Utility/Interfaces/SyncVendingMachineEventPropertiesInterface.php
namespace AppBundle\Entity\VendingMachine\Utility\Interfaces;

interface SyncVendingMachineEventPropertiesInterface
{
    const VENDING_MACHINE_EVENT_ID   = 'id';
    const VENDING_MACHINE_EVENT_TYPE = 'type';
    const VENDING_MACHINE_EVENT_CODE = 'code';
}