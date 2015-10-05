<?php
// AppBundle/Service/Security/Utility/Abstracted/AbstractBoundlessAccess.php
namespace AppBundle\Service\Security\Utility\Abstracted;

use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

abstract class AbstractBoundlessAccess
{
    protected $_authorizationChecker;

    public function setAuthorizationChecker(AuthorizationChecker $authorizationChecker)
    {
        $this->_authorizationChecker = $authorizationChecker;
    }

    abstract public function isGranted($attribute);
}