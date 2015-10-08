<?php
// AppBundle/Service/Sync/SyncDataValidator.php
namespace AppBundle\Service\Sync;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\Validator\Constraints as Assert;

use AppBundle\Service\Sync\Utility\Interfaces\SyncDataInterface,
    AppBundle\Entity\VendingMachine\Utility\Interfaces\SyncVendingMachinePropertiesInterface,
    AppBundle\Entity\VendingMachine\Utility\Interfaces\SyncVendingMachineSyncPropertiesInterface,
    AppBundle\Service\Sync\Utility\Checksum;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SyncDataValidator implements
    SyncDataInterface,
    SyncVendingMachinePropertiesInterface,
    SyncVendingMachineSyncPropertiesInterface
{
    public $_checksum;
    public $_validator;

    public function setChecksum(Checksum $checksum)
    {
        $this->_checksum = $checksum;
    }

    public function setValidator(ValidatorInterface $validator)
    {
        $this->_validator = $validator;
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

        if( $this->_checksum->verifyDataChecksum($requestContent[self::SYNC_CHECKSUM], $requestContent[self::SYNC_DATA]) )
            return FALSE;

        foreach( $requestContent[self::SYNC_DATA][self::VENDING_MACHINE_SYNC_ARRAY] as $value ) {
            if( $value[self::VENDING_MACHINE_SYNC_ID] )
                return FALSE;
        }

        if( empty($requestContent[self::SYNC_DATA][self::VENDING_MACHINE_ARRAY][0][self::VENDING_MACHINE_LOAD_LOADED_AT]) )
            return FALSE;

        $assertDateTime = new Assert\DateTime;

        foreach( $requestContent[self::SYNC_DATA][self::VENDING_MACHINE_ARRAY] as $value ) {
            if( !(count($this->_validator->validate($value[self::VENDING_MACHINE_LOAD_LOADED_AT], $assertDateTime)) === 0) )
                return FALSE;
        }

        return $requestContent;
    }
}