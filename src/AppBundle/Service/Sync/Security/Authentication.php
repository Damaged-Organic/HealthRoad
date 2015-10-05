<?php
// AppBundle/Service/Sync/Security/Authentication.php
namespace AppBundle\Service\Sync\Security;

use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\VendingMachine\VendingMachine;

class Authentication
{
    private $_passwordEncoder;

    public function __construct(PasswordEncoder $passwordEncoder)
    {
        $this->_passwordEncoder = $passwordEncoder;
    }

    public function authenticate(Request $request, VendingMachine $vendingMachine)
    {
        if( !$request->query->has('login') || !$request->query->has('password') )
            return FALSE;

        if( $vendingMachine->getLogin() !== $request->query->get('login') )
            return FALSE;

        if( !$this->_passwordEncoder->isPasswordValid($request->query->get('password'), $vendingMachine->getPassword()) )
            return FALSE;

        return TRUE;
    }
}