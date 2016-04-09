<?php
// src/AppBundle/Model/Notification/Notification.php
namespace AppBundle\Model\Notification;

use DateTime;

use AppBundle\Entity\Customer\Customer,
    AppBundle\Entity\Customer\CustomerNotificationSetting,
    AppBundle\Entity\Student\Student,
    AppBundle\Entity\Product\Product;

/**
 * This entity serves as a transport between data collection and sender services.
 * To avoid additional database queries all objects are stored separately,
 * instead of just Customer entity with multiple proxy objects inside.
 */
class Notification
{
    protected $customer;

    protected $customerNotificationSetting;

    protected $student;

    protected $purchasedAt;

    protected $productsArray;

    protected $smsMessage;

    protected $emailMessage;

    protected $price;

    public function setCustomer(Customer $customer)
    {
        $this->customer = $customer;

        return $this;
    }

    public function getCustomer()
    {
        return $this->customer;
    }

    public function setCustomerNotificationSetting(CustomerNotificationSetting $customerNotificationSetting)
    {
        $this->customerNotificationSetting = $customerNotificationSetting;

        return $this;
    }

    public function getCustomerNotificationSetting()
    {
        return $this->customerNotificationSetting;
    }

    public function setStudent(Student $student)
    {
        $this->student = $student;

        return $this;
    }

    public function getStudent()
    {
        return $this->student;
    }

    public function setPurchasedAt(DateTime $purchasedAt)
    {
        $this->purchasedAt = $purchasedAt;

        return $this;
    }

    public function getPurchasedAt()
    {
        return $this->purchasedAt;
    }

    public function setProductsArray(array $productsArray)
    {
        foreach($productsArray as $product)
        {
            if( $product instanceof Product )
                $this->productsArray[] = $product;
        }

        return $this;
    }

    public function getProductsArray()
    {
        return $this->productsArray;
    }

    public function setSmsMessage($smsMessage)
    {
        $this->smsMessage = $smsMessage;

        return $this;
    }

    public function getSmsMessage()
    {
        return $this->smsMessage;
    }

    public function setEmailMessage($emailMessage)
    {
        $this->emailMessage = $emailMessage;

        return $this;
    }

    public function getEmailMessage()
    {
        return $this->emailMessage;
    }

    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    public function getPrice()
    {
        return $this->price;
    }
}
