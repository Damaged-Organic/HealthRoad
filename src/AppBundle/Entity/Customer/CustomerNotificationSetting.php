<?php
// src/AppBundle/Entity/Customer/CustomerNotificationSetting.php
namespace AppBundle\Entity\Customer;

use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;

use AppBundle\Entity\Utility\Traits\DoctrineMapping\IdMapperTrait;

/**
 * @ORM\Table(name="customers_notification_settings")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Customer\Repository\CustomerNotificationSettingRepository")
 */
class CustomerNotificationSetting
{
    use IdMapperTrait;

     /**
      * @ORM\OneToOne(targetEntity="AppBundle\Entity\Customer\Customer", mappedBy="customerNotificationSetting")
      */
    protected $customer;

    /**
     * @ORM\Column(type="boolean")
     *
     * @Assert\Type(
     *     type="bool",
     *     message="common.boolean.valid"
     * )
     */
    protected $smsOnSync = FALSE;

    /**
     * @ORM\Column(type="boolean")
     *
     * @Assert\Type(
     *     type="bool",
     *     message="common.boolean.valid"
     * )
     */
    protected $smsOnDayEnd = FALSE;

    /**
     * @ORM\Column(type="boolean")
     *
     * @Assert\Type(
     *     type="bool",
     *     message="common.boolean.valid"
     * )
     */
    protected $emailOnSync = FALSE;

    /**
     * @ORM\Column(type="boolean")
     *
     * @Assert\Type(
     *     type="bool",
     *     message="common.boolean.valid"
     * )
     */
    protected $emailOnDayEnd = TRUE;

    /**
     * Set smsOnSync
     *
     * @param boolean $smsOnSync
     * @return CustomerNotificationSetting
     */
    public function setSmsOnSync($smsOnSync)
    {
        $this->smsOnSync = $smsOnSync;

        return $this;
    }

    /**
     * Get smsOnSync
     *
     * @return boolean
     */
    public function getSmsOnSync()
    {
        return $this->smsOnSync;
    }

    /**
     * Set smsOnDayEnd
     *
     * @param boolean $smsOnDayEnd
     * @return CustomerNotificationSetting
     */
    public function setSmsOnDayEnd($smsOnDayEnd)
    {
        $this->smsOnDayEnd = $smsOnDayEnd;

        return $this;
    }

    /**
     * Get smsOnDayEnd
     *
     * @return boolean
     */
    public function getSmsOnDayEnd()
    {
        return $this->smsOnDayEnd;
    }

    /**
     * Set emailOnSync
     *
     * @param boolean $emailOnSync
     * @return CustomerNotificationSetting
     */
    public function setEmailOnSync($emailOnSync)
    {
        $this->emailOnSync = $emailOnSync;

        return $this;
    }

    /**
     * Get emailOnSync
     *
     * @return boolean
     */
    public function getEmailOnSync()
    {
        return $this->emailOnSync;
    }

    /**
     * Set emailOnDayEnd
     *
     * @param boolean $emailOnDayEnd
     * @return CustomerNotificationSetting
     */
    public function setEmailOnDayEnd($emailOnDayEnd)
    {
        $this->emailOnDayEnd = $emailOnDayEnd;

        return $this;
    }

    /**
     * Get emailOnDayEnd
     *
     * @return boolean
     */
    public function getEmailOnDayEnd()
    {
        return $this->emailOnDayEnd;
    }

    /**
     * Set customer
     *
     * @param \AppBundle\Entity\Customer\Customer $customer
     * @return CustomerNotificationSetting
     */
    public function setCustomer(\AppBundle\Entity\Customer\Customer $customer = null)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * Get customer
     *
     * @return \AppBundle\Entity\Customer\Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }
}
