<?php
// AppBundle/Service/Security/Utility/Interfaces/UserRoleListInterface.php
namespace AppBundle\Service\Security\Utility\Interfaces;

interface UserRoleListInterface
{
    const ROLE_USER       = "ROLE_USER";

    const ROLE_CUSTOMER   = "ROLE_CUSTOMER";
    const ROLE_EMPLOYEE   = "ROLE_EMPLOYEE";

    const ROLE_ACCOUNTANT = "ROLE_ACCOUNTANT";
    const ROLE_REGISTRAR  = "ROLE_REGISTRAR";
    const ROLE_MANAGER    = "ROLE_MANAGER";
    const ROLE_ADMIN      = "ROLE_ADMIN";
    const ROLE_SUPERADMIN = "ROLE_SUPERADMIN";
}