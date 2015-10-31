<?php
// src/AppBundle/Service/Sync/Utility/Checksum.php
namespace AppBundle\Service\Sync\Utility;

class Checksum
{
    public function getDataChecksum($data)
    {
        return hash('sha256', json_encode($data));
    }

    public function verifyDataChecksum($checksum, $data)
    {
        return $checksum === $this->getDataChecksum($data);
    }
}