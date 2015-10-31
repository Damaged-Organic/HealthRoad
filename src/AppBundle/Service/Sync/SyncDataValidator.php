<?php
// AppBundle/Service/Sync/SyncDataValidator.php
namespace AppBundle\Service\Sync;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\Validator\Constraints as Assert,
    Symfony\Component\Validator\Validator\ValidatorInterface;

use Doctrine\ORM\EntityManager;

use AppBundle\Service\Sync\Utility\Interfaces\SyncDataInterface,
    AppBundle\Entity\VendingMachine\Utility\Interfaces\SyncVendingMachinePropertiesInterface,
    AppBundle\Entity\VendingMachine\Utility\Interfaces\SyncVendingMachineSyncPropertiesInterface,
    AppBundle\Entity\Purchase\Purchase,
    AppBundle\Entity\VendingMachine\VendingMachineEvent,
    AppBundle\Entity\VendingMachine\VendingMachineSync,
    AppBundle\Service\Sync\Utility\Checksum,
    AppBundle\Validator\Constraints as CustomAssert;

class SyncDataValidator implements
    SyncDataInterface,
    SyncVendingMachinePropertiesInterface,
    SyncVendingMachineSyncPropertiesInterface
{
    private $_manager;
    private $_validator;

    private $_checksum;

    public function setManager(EntityManager $manager)
    {
        $this->_manager = $manager;
    }

    public function setValidator(ValidatorInterface $validator)
    {
        $this->_validator = $validator;
    }

    public function setChecksum(Checksum $checksum)
    {
        $this->_checksum = $checksum;
    }

    public function validateSyncSequence($vendingMachine, $type, $data)
    {
        $vendingMachineSync = $this->_manager->getRepository('AppBundle:VendingMachine\VendingMachineSync')->findOneBy([
            'vendingMachine'       => $vendingMachine,
            'vendingMachineSyncId' => $data[self::SYNC_DATA][self::VENDING_MACHINE_SYNC_ARRAY][0][self::VENDING_MACHINE_SYNC_ID],
            'syncedType'           => $type
        ]);

        return $vendingMachineSync;
    }

    public function validateVendingMachineSyncData(Request $request)
    {
        if( !$request->query->has(self::VENDING_MACHINE_SYNC_TYPE) )
            return FALSE;

        return [
            self::VENDING_MACHINE_SYNC_TYPE => $request->query->get(self::VENDING_MACHINE_SYNC_TYPE)
        ];
    }

    public function validateVendingMachineData(Request $request)
    {
        $requestContent = json_decode($request->getContent(), TRUE);

        if( empty($requestContent[self::SYNC_CHECKSUM]) || empty($requestContent[self::SYNC_DATA]) )
            return FALSE;

        if( !$this->_checksum->verifyDataChecksum($requestContent[self::SYNC_CHECKSUM], $requestContent[self::SYNC_DATA]) )
            return FALSE;

        /*foreach( $requestContent[self::SYNC_DATA][VendingMachineSync::getSyncArrayName()] as $value ) {
            if( !$value[self::VENDING_MACHINE_SYNC_ID] )
                return FALSE;
        }*/

        // specific validation

        $assertDateTime = new Assert\DateTime;

        foreach( $requestContent[self::SYNC_DATA][self::VENDING_MACHINE_ARRAY] as $value ) {
            if( !(count($this->_validator->validate($value[self::VENDING_MACHINE_LOAD_LOADED_AT], $assertDateTime)) === 0) )
                return FALSE;
        }

        return $requestContent;
    }

    public function validatePurchaseData(Request $request)
    {
        $requestContent = json_decode($request->getContent(), TRUE);

        if( empty($requestContent[self::SYNC_CHECKSUM]) || empty($requestContent[self::SYNC_DATA]) )
            return FALSE;

        if( !$this->_checksum->verifyDataChecksum($requestContent[self::SYNC_CHECKSUM], $requestContent[self::SYNC_DATA]) )
            return FALSE;

        foreach( $requestContent[self::SYNC_DATA][VendingMachineSync::getSyncArrayName()] as $value ) {
            if( !$value[self::VENDING_MACHINE_SYNC_ID] )
                return FALSE;
        }

        // specific validation

        if( empty($requestContent[self::SYNC_DATA][Purchase::getSyncArrayName()]) )
            return FALSE;

        $assertDateTime = new Assert\DateTime;
        $assertIsPrice  = new CustomAssert\IsPriceConstraint;

        foreach( $requestContent[self::SYNC_DATA][Purchase::getSyncArrayName()] as $value )
        {
            if( empty($value[Purchase::PURCHASE_SYNC_ID]) )
                return FALSE;

            if( empty($value[Purchase::PURCHASE_PURCHASED_AT]) )
                return FALSE;
            $datetimeErrors = count($this->_validator->validate($value[Purchase::PURCHASE_PURCHASED_AT], $assertDateTime));
            if( $datetimeErrors !== 0 )
                return FALSE;

            if( empty($value[Purchase::PURCHASE_PRODUCT_ID]) )
                return FALSE;

            if( empty($value[Purchase::PURCHASE_SYNC_PRODUCT_PRICE]) )
                return FALSE;
            $isPriceErrors = count($this->_validator->validate($value[Purchase::PURCHASE_SYNC_PRODUCT_PRICE], $assertIsPrice));
            if( $isPriceErrors !== 0 )
                return FALSE;

            if( empty($value[Purchase::PURCHASE_NFC_CODE]) )
                return FALSE;
        }

        return $requestContent;
    }

    public function validateEventData(Request $request)
    {
        $requestContent = json_decode($request->getContent(), TRUE);

        if( empty($requestContent[self::SYNC_CHECKSUM]) || empty($requestContent[self::SYNC_DATA]) )
            return FALSE;

        if( !$this->_checksum->verifyDataChecksum($requestContent[self::SYNC_CHECKSUM], $requestContent[self::SYNC_DATA]) )
            return FALSE;

        /*foreach( $requestContent[self::SYNC_DATA][VendingMachineSync::getSyncArrayName()] as $value ) {
            if( !$value[self::VENDING_MACHINE_SYNC_ID] )
                return FALSE;
        }*/

        // specific validation

        $assertDateTime = new Assert\DateTime;

        foreach( $requestContent[self::SYNC_DATA][VendingMachineEvent::getSyncArrayName()] as $value )
        {
            if( empty($value[VendingMachineEvent::VENDING_MACHINE_EVENT_ID]) )
                return FALSE;

            if( empty($value[VendingMachineEvent::VENDING_MACHINE_EVENT_DATETIME]) )
                return FALSE;
            $datetimeErrors = count($this->_validator->validate($value[VendingMachineEvent::VENDING_MACHINE_EVENT_DATETIME], $assertDateTime));
            if( $datetimeErrors !== 0 )
                return FALSE;

            if( empty($value[VendingMachineEvent::VENDING_MACHINE_EVENT_TYPE]) )
                return FALSE;

            if( empty($value[VendingMachineEvent::VENDING_MACHINE_EVENT_CODE]) )
                return FALSE;

            if( empty($value[VendingMachineEvent::VENDING_MACHINE_EVENT_MESSAGE]) )
                return FALSE;
        }

        return $requestContent;
    }
}