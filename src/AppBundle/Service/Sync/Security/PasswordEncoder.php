<?php
// AppBundle/Service/Sync/Security/PasswordEncoder.php
namespace AppBundle\Service\Sync\Security;

class PasswordEncoder
{
    public function encodePassword($password)
    {
        return password_hash($password, PASSWORD_BCRYPT, [
            'cost' => 12
        ]);
    }

    public function isPasswordValid($password, $encoded)
    {
        return password_verify($password, $encoded);
    }
}