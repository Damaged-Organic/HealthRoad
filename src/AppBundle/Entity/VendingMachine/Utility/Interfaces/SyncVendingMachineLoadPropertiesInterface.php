<?php
// AppBundle/Entity/VendingMachine/Utility/Interfaces/SyncVendingMachineLoadPropertiesInterface.php
namespace AppBundle\Entity\VendingMachine\Utility\Interfaces;

interface SyncVendingMachineLoadPropertiesInterface
{
    /*
     * Frankly, I don't like those constants values because they just wrong.
     * I was too tired to argue with ones who made them up.
     * Forgive me.
     */

    const VENDING_MACHINE_LOAD_ARRAY = 'fill';

    const VENDING_MACHINE_LOAD_PRODUCT_ID       = 'id';
    const VENDING_MACHINE_LOAD_PRODUCT_QUANTITY = 'count';
    const VENDING_MACHINE_LOAD_SPRING_POSITION  = 'position';
}