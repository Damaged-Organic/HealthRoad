<?php
// src/AppBundle/Service/Security/PaymentReceiptBoundlessAccess.php
namespace AppBundle\Service\Security;

use AppBundle\Service\Security\Utility\Abstracted\AbstractBoundlessAccess,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface;

class PaymentReceiptBoundlessAccess extends AbstractBoundlessAccess implements UserRoleListInterface
{
    const PAYMENT_RECEIPT_READ = 'payment_receipt_read';

    const PAYMENT_RECEIPT_REPLENISH = 'payment_receipt_replenish';

    public function isGranted($attribute)
    {
        switch($attribute)
        {
            case self::PAYMENT_RECEIPT_READ:
                return $this->_authorizationChecker->isGranted(self::ROLE_EMPLOYEE);
            break;

            /*
             * KLUDGE: performs check on specific user role only, avoiding
             * the role hierarchy
             */
            case self::PAYMENT_RECEIPT_REPLENISH:
                if( $this->_authorizationChecker->isGranted(self::ROLE_SUPERADMIN) )
                    return FALSE;

                if( $this->_authorizationChecker->isGranted(self::ROLE_ADMIN) )
                    return FALSE;

                if( $this->_authorizationChecker->isGranted(self::ROLE_ACCOUNTANT) )
                    return TRUE;

                return FALSE;
            break;

            default:
                return FALSE;
            break;
        }
    }
}
