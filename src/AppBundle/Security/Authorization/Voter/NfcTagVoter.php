<?php
// AppBundle/Security/Authorization/Voter/NfcTagVoter.php
namespace AppBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\User\UserInterface;

use AppBundle\Security\Authorization\Voter\Utility\Extended\ExtendedAbstractVoter,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface;

class NfcTagVoter extends ExtendedAbstractVoter implements UserRoleListInterface
{
    const NFC_TAG_READ   = "nfc_tag_read";
    const NFC_TAG_UPDATE = "nfc_tag_update";
    const NFC_TAG_DELETE = "nfc_tag_delete";

    const NFC_TAG_BIND   = "nfc_tag_bind";

    protected function getSupportedAttributes()
    {
        return [
            self::NFC_TAG_READ,
            self::NFC_TAG_UPDATE,
            self::NFC_TAG_DELETE,
            self::NFC_TAG_BIND
        ];
    }

    protected function getSupportedClasses()
    {
        return ['AppBundle\Entity\NfcTag\NfcTag'];
    }

    protected function isGranted($attribute, $nfcTag, $user = NULL)
    {
        if (!$user instanceof UserInterface)
            return FALSE;

        switch($attribute)
        {
            case self::NFC_TAG_READ:
                return $this->read($nfcTag, $user);
            break;

            case self::NFC_TAG_UPDATE:
                return $this->update($nfcTag, $user);
            break;

            case self::NFC_TAG_DELETE:
                return $this->delete($user);
            break;

            case self::NFC_TAG_BIND:
                return $this->bind($user);
            break;

            default:
                return FALSE;
            break;
        }
    }

    protected function read($nfcTag, $user = NULL)
    {
        if( $nfcTag->getPseudoDeleted() )
        {
            return ( $this->hasRole($user, self::ROLE_ADMIN) )
                ? TRUE
                : FALSE;
        }

        if( $this->hasRole($user, self::ROLE_EMPLOYEE) )
            return TRUE;

        return FALSE;
    }

    protected function update($nfcTag, $user = NULL)
    {
        if( $nfcTag->getPseudoDeleted() )
            return FALSE;

        if( $this->hasRole($user, self::ROLE_ADMIN) )
            return TRUE;

        return FALSE;
    }

    protected function delete($user = NULL)
    {
        if( $this->hasRole($user, self::ROLE_ADMIN) )
            return TRUE;

        return FALSE;
    }

    protected function bind($user)
    {
        if( $this->hasRole($user, self::ROLE_ADMIN) )
            return TRUE;

        return FALSE;
    }
}