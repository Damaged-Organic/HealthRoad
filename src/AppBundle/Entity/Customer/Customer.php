<?php
// AppBundle/Entity/Customer/Customer.php
namespace AppBundle\Entity\Customer;

use Serializable;

use Symfony\Component\Security\Core\User\AdvancedUserInterface,
    Symfony\Component\Validator\Constraints as Assert,
    Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection;

use AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Entity\Utility\Traits\DoctrineMapping\IdMapperTrait,
    AppBundle\Validator\Constraints as CustomAssert,
    AppBundle\Entity\Customer\CustomerNotificationSetting;

/**
 * @ORM\Table(name="customers")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Customer\Repository\CustomerRepository")
 *
 * @UniqueEntity(fields="phoneNumber", message="customer.phone_number.unique")
 *
 * @Assert\GroupSequence({"Customer", "Strict", "Create", "Update"})
 */
class Customer implements AdvancedUserInterface, UserRoleListInterface, Serializable
{
    use IdMapperTrait;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Employee\Employee", inversedBy="customers")
     * @ORM\JoinColumn(name="employee_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $employee;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Student\Student", mappedBy="customer")
     */
    protected $students;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Customer\CustomerNotificationSetting", inversedBy="customer", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="customer_notification_setting_id", referencedColumnName="id")
     */
    protected $customerNotificationSetting;

    protected $username;

    /**
     * @ORM\Column(type="string", length=20, unique=true)
     *
     * @Assert\NotBlank(
     *      message="customer.phone_number.not_blank",
     *      groups={"Create"}
     * )
     *
     * @CustomAssert\IsPhoneNumberConstraint
     */
    protected $phoneNumber;

    /**
     * @ORM\Column(type="string", length=64)
     *
     * Assert\NotBlank(
     *      message="customer.password.not_blank",
     *      groups={"Create"}
     * )
     * @Assert\Length(
     *      min=6,
     *      minMessage="customer.password.length.min",
     * )
     */
    protected $password;

    /**
     * @ORM\Column(type="boolean")
     *
     * @Assert\Type(
     *     type="bool",
     *     message="customer.is_enabled.type",
     *     groups={"Update"}
     * )
     */
    protected $isEnabled;

    /**
     * @ORM\Column(type="string", length=100)
     *
     * @Assert\Length(
     *      min=2,
     *      max=100,
     *      minMessage="common.human_name.length.min",
     *      maxMessage="common.human_name.length.max"
     * )
     * @Assert\Regex(
     *     pattern="/^[a-zA-Z\p{L}-]+$/u",
     *     message="common.human_name.regex"
     * )
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=100)
     *
     * @Assert\Length(
     *      min=2,
     *      max=100,
     *      minMessage="common.human_name.length.min",
     *      maxMessage="common.human_name.length.max"
     * )
     * @Assert\Regex(
     *     pattern="/^[a-zA-Z\p{L}-]+$/u",
     *     message="common.human_name.regex"
     * )
     */
    protected $surname;

    /**
     * @ORM\Column(type="string", length=100)
     *
     * @Assert\Length(
     *      min=2,
     *      max=100,
     *      minMessage="common.human_name.length.min",
     *      maxMessage="common.human_name.length.max"
     * )
     * @Assert\Regex(
     *     pattern="/^[a-zA-Z\p{L}-]+$/u",
     *     message="common.human_name.regex"
     * )
     */
    protected $patronymic;

    /**
     * @ORM\Column(type="string", length=60, nullable=true)
     *
     * @Assert\Email(
     *      message="common.email.valid",
     *      checkMX=true
     * )
     */
    protected $email;

    public function __construct()
    {
        $this->students = new ArrayCollection;

        $this
            ->setIsEnabled(TRUE)
        ;

        // For automatic creation of the related entity
        $this->setCustomerNotificationSetting(new CustomerNotificationSetting);
    }

    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set phoneNumber
     *
     * @param string $phoneNumber
     * @return Customer
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * Get phoneNumber
     *
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return Customer
     */
    public function setPassword($password)
    {
        if( !is_null($password) ) {
            $this->password = $password;
        }

        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getRoles()
    {
        return [self::ROLE_CUSTOMER];
    }

    public function getSalt()
    {
        return NULL;
    }

    public function eraseCredentials() {}

    public function isAccountNonExpired()
    {
        return TRUE;
    }

    public function isAccountNonLocked()
    {
        return TRUE;
    }

    public function isCredentialsNonExpired()
    {
        return TRUE;
    }

    /**
     * Set isEnabled
     *
     * @param boolean $isEnabled
     * @return Customer
     */
    public function setIsEnabled($isEnabled)
    {
        $this->isEnabled = $isEnabled;

        return $this;
    }

    /**
     * Get isEnabled
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->isEnabled;
    }

    /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->phoneNumber,
            $this->password,
            $this->isEnabled,
        ));
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->phoneNumber,
            $this->password,
            $this->isEnabled,
            ) = unserialize($serialized);
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Customer
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set surname
     *
     * @param string $surname
     * @return Customer
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * Get surname
     *
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Set patronymic
     *
     * @param string $patronymic
     * @return Customer
     */
    public function setPatronymic($patronymic)
    {
        $this->patronymic = $patronymic;

        return $this;
    }

    /**
     * Get patronymic
     *
     * @return string
     */
    public function getPatronymic()
    {
        return $this->patronymic;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Customer
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Add student
     *
     * @param \AppBundle\Entity\Student\Student $student
     * @return Customer
     */
    public function addStudent(\AppBundle\Entity\Student\Student $student)
    {
        $student->setCustomer($this);
        $this->students[] = $student;

        return $this;
    }

    /**
     * Remove students
     *
     * @param \AppBundle\Entity\Student\Student $students
     */
    public function removeStudent(\AppBundle\Entity\Student\Student $students)
    {
        $this->students->removeElement($students);
    }

    /**
     * Get students
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStudents()
    {
        return $this->students;
    }

    /**
     * Set employee
     *
     * @param \AppBundle\Entity\Employee\Employee $employee
     * @return Customer
     */
    public function setEmployee(\AppBundle\Entity\Employee\Employee $employee)
    {
        $this->employee = $employee;

        return $this;
    }

    /**
     * Get employee
     *
     * @return \AppBundle\Entity\Employee\Employee
     */
    public function getEmployee()
    {
        return $this->employee;
    }

    /**
     * Set customerNotificationSetting
     *
     * @param \AppBundle\Entity\Customer\CustomerNotificationSetting $customerNotificationSetting
     * @return Customer
     */
    public function setCustomerNotificationSetting(\AppBundle\Entity\Customer\CustomerNotificationSetting $customerNotificationSetting = null)
    {
        $this->customerNotificationSetting = $customerNotificationSetting;

        return $this;
    }

    /**
     * Get customerNotificationSetting
     *
     * @return \AppBundle\Entity\Customer\CustomerNotificationSetting
     */
    public function getCustomerNotificationSetting()
    {
        return $this->customerNotificationSetting;
    }

    /**
     * @Assert\True(message="customer.password.legal", groups={"Strict"})
     */
    public function isPasswordLegal()
    {
        return ($this->password !== $this->phoneNumber);
    }

    public function getFullName()
    {
        if( !$this->patronymic && !$this->name && !$this->surname )
            return NULL;

        return "{$this->surname} {$this->name} {$this->patronymic}";
    }

    public function getTotalLimit()
    {
        $totalLimit = 0;

        foreach( $this->getStudents() as $student )
        {
            if( !$student->getPseudoDeleted() )
                $totalLimit = bcadd($totalLimit, $student->getTotalLimit(), 2);
        }

        return $totalLimit;
    }
}
