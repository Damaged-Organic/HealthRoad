<?php
// AppBundle/Security/Authorization/Voter/Utility/Extended/ExtendedAbstractVoter.php
namespace AppBundle\Security\Authorization\Voter\Utility\Extended;

use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter,
    Symfony\Component\Security\Core\Role\RoleHierarchy;

abstract class ExtendedAbstractVoter extends AbstractVoter
{
    protected $_roleHeirarchy;

    public function setRoleHierarchy(RoleHierarchy $roleHeirarchy)
    {
        $this->_roleHeirarchy = $roleHeirarchy;
    }

    protected function hasRole($token, $targetRole)
    {
        $reachableRoles = $this->_roleHeirarchy->getReachableRoles($token->getRoles());

        foreach($reachableRoles as $role) {
            if ($role->getRole() == $targetRole) return TRUE;
        }

        return FALSE;
    }
}