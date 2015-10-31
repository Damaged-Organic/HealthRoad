<?php
// AppBundle/Entity/VendingMachine/Repository/VendingMachineEventRepository.php
namespace AppBundle\Entity\VendingMachine\Repository;

use AppBundle\Entity\Utility\Extended\ExtendedEntityRepository;

class VendingMachineEventRepository extends ExtendedEntityRepository
{
    public function rawInsertVendingMachineEvents(array $eventsArray)
    {
        $queryString = '';

        foreach( $eventsArray as $event )
        {
            $queryString .= " (
                '{$event->getVendingMachine()->getId()}',
                '{$event->getSyncEventId()}',
                '{$event->getOccurredAt()}',
                '{$event->getType()}',
                '{$event->getCode()}',
                '{$event->getMessage()}'
            ),";
        }

        $queryString = substr($queryString, 0, -1);

        $queryString = "
            INSERT INTO vending_machines_events (
                vending_machine_id,
                sync_event_id,
                occurred_at,
                type,
                code,
                message
            ) VALUES " . $queryString;

        $stmt = $this->getEntityManager()->getConnection()->prepare($queryString);

        $stmt->execute();
    }
}