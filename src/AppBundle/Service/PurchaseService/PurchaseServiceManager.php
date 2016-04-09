<?php
// src/AppBundle/Service/PurchaseService/PurchaseServiceManager.php
namespace AppBundle\Service\PurchaseService;

use DateTime;

use Symfony\Component\Translation\TranslatorInterface;

use Doctrine\ORM\EntityManager;

use AppBundle\Entity\Student\Student,
    AppBundle\Entity\Setting\SettingDecimal,
    AppBundle\Entity\PurchaseService\PurchaseService,
    AppBundle\Entity\Payment\PaymentReceipt,
    AppBundle\Model\Notification\Notification;

class PurchaseServiceManager
{
    private $_manager;
    private $_translator;

    public function setManager(EntityManager $_manager)
    {
        $this->_manager = $_manager;
    }

    public function setTranslator(TranslatorInterface $translator)
    {
        $this->_translator = $translator;
    }

    public function getPurchaseService()
    {
        return new PurchaseService;
    }

    # NFC Tag activation

    public function validateStatusActivationNfcTag(Student $student)
    {
        return !$student->getNfcTag()->getIsActivated();
    }

    public function validateBalanceActivationNfcTag(Student $student, SettingDecimal $settingNfcTagActivationFee)
    {
        $compare = bccomp($student->getTotalLimit(), $settingNfcTagActivationFee->getSettingValue(), 2);

        return $compare === 1;
    }

    public function purchaseActivationNfcTag(Student $student, SettingDecimal $settingNfcTagActivationFee)
    {
        if( $student->getNfcTag()->getIsActivated() )
            return;

        $this->processActivationNfcTag($student, $settingNfcTagActivationFee);
        $this->recordActivationNfcTag($student, $settingNfcTagActivationFee);

        $this->_manager->flush();
    }

    public function purchaseActivationNfcTagFromPaymentReceipts(array $paymentReceipts, SettingDecimal $settingNfcTagActivationFee)
    {
        foreach( $paymentReceipts as $paymentReceipt )
        {
            if( $paymentReceipt instanceof PaymentReceipt )
            {
                $student = $paymentReceipt->getStudent();

                if( !$this->validateStatusActivationNfcTag($student) )
                    continue;

                if( !$this->validateBalanceActivationNfcTag($student, $settingNfcTagActivationFee) )
                    continue;

                $this->processActivationNfcTag($student, $settingNfcTagActivationFee);
                $this->recordActivationNfcTag($student, $settingNfcTagActivationFee);
            }
        }
    }

    public function freeActivationNfcTag(Student $student)
    {
        if( $student->getNfcTag()->getIsActivated() )
            return;

        $student->getNfcTag()->activate();

        $this->_manager->persist($student);
        $this->_manager->flush();
    }

    private function processActivationNfcTag(Student $student, SettingDecimal $settingNfcTagActivationFee)
    {
        $student->setTotalLimit(
            bcsub($student->getTotalLimit(), $settingNfcTagActivationFee->getSettingValue(), 2)
        );

        $student->getNfcTag()->activate();

        $this->_manager->persist($student);
    }

    private function recordActivationNfcTag(Student $student, SettingDecimal $settingNfcTagActivationFee)
    {
        $item  = $this->getActivationNfcTagItem();
        $price = $settingNfcTagActivationFee->getSettingValue();

        $purchaseService = $this->getPurchaseService();

        $purchaseService
            ->setStudent($student)
            ->setNfcTag($student->getNfcTag())
            ->setItem($item)
            ->setPrice($price)
            ->setPurchasedAt(new DateTime)
        ;

        $this->_manager->persist($purchaseService);
    }

    private function getActivationNfcTagItem()
    {
        return $this->_translator->trans('item.nfc_tag.activation', [], 'purchasesService', 'ua');
    }

    # SMS sending

    public function validateStudentBalanceForSms(Student $student, SettingDecimal $settingStudentWarningLimit)
    {
        $compare = bccomp($student->getTotalLimit(), $settingStudentWarningLimit->getSettingValue(), 2);

        return $compare === 1;
    }

    public function purchaseNotificationMessages(array $notifications, SettingDecimal $settingSmsExchangeRate)
    {
        foreach( $notifications as $notification )
        {
            if( !($notification instanceof Notification) )
                return;

            if( $this->processNotification($notification, $settingSmsExchangeRate) )
            {
                $this->recordNotification($notification, $settingSmsExchangeRate);
            }
        }

        $this->_manager->flush();
    }

    public function processNotification(Notification $notification, SettingDecimal $settingSmsExchangeRate)
    {
        if( !$notification->getStudent() )
            return FALSE;

        $price = bcmul($notification->getPrice(), $settingSmsExchangeRate->getSettingValue(), 2);

        if( !$price )
            return FALSE;

        $notification->getStudent()->setTotalLimit(
            bcsub($notification->getStudent()->getTotalLimit(), $price, 2)
        );

        $this->_manager->persist($notification->getStudent());

        return TRUE;
    }

    public function recordNotification(Notification $notification, SettingDecimal $settingSmsExchangeRate)
    {
        if( !$notification->getStudent() )
            return FALSE;

        if( !$notification->getStudent()->getNfcTag() )
            return FALSE;

        $item  = $this->getNotificationMessageItem();
        $price = bcmul($notification->getPrice(), $settingSmsExchangeRate->getSettingValue(), 2);

        $purchaseService = $this->getPurchaseService();

        $purchaseService
            ->setStudent($notification->getStudent())
            ->setNfcTag($notification->getStudent()->getNfcTag())
            ->setItem($item)
            ->setPrice($price)
            ->setPurchasedAt(new DateTime)
        ;

        $this->_manager->persist($purchaseService);
    }

    private function getNotificationMessageItem()
    {
        return $this->_translator->trans('item.notification.message', [], 'purchasesService', 'ua');
    }
}
