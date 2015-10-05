<?php
// AppBundle/Entity/Employee/Employee.php
namespace AppBundle\Entity\Employee;

use Serializable;

use Symfony\Component\Security\Core\User\AdvancedUserInterface,
    Symfony\Component\Validator\Constraints as Assert,
    Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection;

use AppBundle\Entity\Utility\Traits\DoctrineMapping\IdMapperTrait,
    AppBundle\Validator\Constraints as CustomAssert;

/**
 * @ORM\Table(name="employees")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Employee\Repository\EmployeeRepository")
 *
 * @UniqueEntity(fields="username", message="employee.username.unique")
 *
 * @Assert\GroupSequence({"Employee", "Strict", "Create", "Update"})
 */
class Employee implements AdvancedUserInterface, Serializable
{
    use IdMapperTrait;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Employee\EmployeeGroup", inversedBy="employees")
     * @ORM\JoinColumn(name="employee_group_id", referencedColumnName="id")
     */
    protected $employeeGroup;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Region\Region", mappedBy="employee")
     */
    protected $regions;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\School\School", inversedBy="employees")
     * @ORM\JoinTable(name="employees_schools")
     */
    protected $schools;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Customer\Customer", mappedBy="employee")
     */
    protected $customers;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Student\Student", mappedBy="employee")
     */
    protected $students;

    /**
     * @ORM\Column(type="string", length=25, unique=true)
     *
     * @Assert\NotBlank(message="employee.username.not_blank")
     * @Assert\Length(
     *      min=3,
     *      max=200,
     *      minMessage="employee.username.length.min",
     *      maxMessage="employee.username.length.max"
     * )
     */
    protected $username;

    /**
     * @ORM\Column(type="string", length=64)
     *
     * @Assert\NotBlank(
     *      message="employee.password.not_blank",
     *      groups={"Create"}
     * )
     */
    protected $password;

    /**
     * @ORM\Column(type="boolean")
     *
     * @Assert\Type(
     *     type="bool",
     *     message="employee.is_enabled.type",
     *     groups={"Update"}
     * )
     */
    protected $isEnabled;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
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
     * @ORM\Column(type="string", length=100, nullable=true)
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
     * @ORM\Column(type="string", length=100, nullable=true)
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

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     *
     * @CustomAssert\IsPhoneNumberConstraint
     */
    protected $phoneNumber;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     *
     * @CustomAssert\IsSkypeNameConstraint
     */
    protected $skypeName;

    public function __construct()
    {
        $this->regions   = new ArrayCollection;
        $this->schools   = new ArrayCollection;
        $this->customers = new ArrayCollection;
        $this->students  = new ArrayCollection;

        $this
            ->setIsEnabled(TRUE)
        ;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return Employee
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return Employee
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
        return [$this->employeeGroup];
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
     * @return Employee
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

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            $this->isEnabled,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            $this->isEnabled,
            ) = unserialize($serialized);
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Employee
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
     * @return Employee
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
     * @return Employee
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
     * @return Employee
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
     * Set phoneNumber
     *
     * @param string $phoneNumber
     * @return Employee
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
     * Set skypeName
     *
     * @param string $skypeName
     * @return Employee
     */
    public function setSkypeName($skypeName)
    {
        $this->skypeName = $skypeName;

        return $this;
    }

    /**
     * Get skypeName
     *
     * @return string 
     */
    public function getSkypeName()
    {
        return $this->skypeName;
    }

    /**
     * Set employeeGroup
     *
     * @param \AppBundle\Entity\Employee\EmployeeGroup $employeeGroup
     * @return Employee
     */
    public function setEmployeeGroup(\AppBundle\Entity\Employee\EmployeeGroup $employeeGroup = null)
    {
        $this->employeeGroup = $employeeGroup;

        return $this;
    }

    /**
     * Get employeeGroup
     *
     * @return \AppBundle\Entity\Employee\EmployeeGroup
     */
    public function getEmployeeGroup()
    {
        return $this->employeeGroup;
    }

    /**
     * Add regions
     *
     * @param \AppBundle\Entity\Region\Region $region
     * @return Employee
     */
    public function addRegion(\AppBundle\Entity\Region\Region $region)
    {
        $region->setEmployee($this);
        $this->regions[] = $region;

        return $this;
    }

    /**
     * Remove regions
     *
     * @param \AppBundle\Entity\Region\Region $regions
     */
    public function removeRegion(\AppBundle\Entity\Region\Region $regions)
    {
        $this->regions->removeElement($regions);
    }

    /**
     * Add schools
     *
     * @param \AppBundle\Entity\School\School $school
     * @return Employee
     */
    public function addSchool(\AppBundle\Entity\School\School $school)
    {
        $school->addEmployee($this);
        $this->schools[] = $school;

        return $this;
    }

    /**
     * Remove schools
     *
     * @param \AppBundle\Entity\School\School $schools
     */
    public function removeSchool(\AppBundle\Entity\School\School $schools)
    {
        $this->schools->removeElement($schools);
    }

    /**
     * Get schools
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSchools()
    {
        return $this->schools;
    }

    /**
     * Get regions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRegions()
    {
        return $this->regions;
    }

    /**
     * Add customers
     *
     * @param \AppBundle\Entity\Customer\Customer $customers
     * @return Employee
     */
    public function addCustomer(\AppBundle\Entity\Customer\Customer $customers)
    {
        $this->customers[] = $customers;

        return $this;
    }

    /**
     * Remove customers
     *
     * @param \AppBundle\Entity\Customer\Customer $customers
     */
    public function removeCustomer(\AppBundle\Entity\Customer\Customer $customers)
    {
        $this->customers->removeElement($customers);
    }

    /**
     * Get customers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCustomers()
    {
        return $this->customers;
    }

    /**
     * Add students
     *
     * @param \AppBundle\Entity\Student\Student $students
     * @return Employee
     */
    public function addStudent(\AppBundle\Entity\Student\Student $students)
    {
        $this->students[] = $students;

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
     * @Assert\True(message="employee.password.legal", groups={"Strict"})
     */
    public function isPasswordLegal()
    {
        return ($this->password !== $this->username);
    }
}