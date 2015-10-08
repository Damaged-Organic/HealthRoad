<?php
// AppBundle/Service/Sync/SyncDataValidator.php
namespace AppBundle\Service\Sync;

use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\VendingMachine\Utility\Interfaces\SyncVendingMachineSyncPropertiesInterface;

class SyncDataValidator implements SyncVendingMachineSyncPropertiesInterface
{
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

        if( empty($requestContent['checksum']) || empty($requestContent['data']) )
            return FALSE;

        if( $requestContent['checksum'] !== hash('sha256', json_encode($requestContent['data'])) )
            return FALSE;

        if( empty($requestContent['data']['sync']['sync-id']) )
            return FALSE;

        if( empty($requestContent['data']['vending-machine']['load-datetime']) )
            return FALSE;

        return TRUE;
    }
}