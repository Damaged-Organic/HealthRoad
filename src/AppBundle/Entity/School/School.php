<?php
// AppBundle/Entity/School/School.php
namespace AppBundle\Entity\School;

use Symfony\Component\Validator\Constraints as Assert,
    Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection;

use AppBundle\Entity\Utility\Traits\DoctrineMapping\IdMapperTrait,
    AppBundle\Validator\Constraints as CustomAssert;

/**
 * @ORM\Table(name="schools")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\School\Repository\SchoolRepository")
 */
class School
{
    use IdMapperTrait;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Employee\Employee", mappedBy="schools")
     */
    protected $employees;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Settlement\Settlement", inversedBy="schools")
     * @ORM\JoinColumn(name="settlement_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $settlement;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\VendingMachine\VendingMachine", mappedBy="school")
     */
    protected $vendingMachines;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Student\Student", mappedBy="school")
     */
    protected $students;

    /**
     * @ORM\Column(type="string", length=250)
     *
     * @Assert\NotBlank(message="school.name_school.not_blank")
     * @Assert\Length(
     *      min=2,
     *      max=250,
     *      minMessage="school.name_school.length.min",
     *      maxMessage="school.name_school.length.max"
     * )
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=500)
     *
     * @Assert\NotBlank(message="school.address.not_blank")
     * @Assert\Length(
     *      min=2,
     *      max=500,
     *      minMessage="school.address.length.min",
     *      maxMessage="school.address.length.max"
     * )
     */
    protected $address;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @Assert\Range(
     *      min=1,
     *      max=100000,
     *      minMessage="school.students_quantity.range.min",
     *      maxMessage="school.students_quantity.range.max"
     * )
     */
    protected $studentsQuantity;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     *
     * @CustomAssert\IsPhoneNumberConstraint
     */
    protected $phoneNumberSchool;

    /**
     * @ORM\Column(type="string", length=60, nullable=true)
     *
     * @Assert\Email(
     *      message="common.email.valid",
     *      checkMX=true
     * )
     */
    protected $emailSchool;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     *
     * @Assert\Length(
     *      min=2,
     *      max=500,
     *      minMessage="school.name_headmaster.length.min",
     *      maxMessage="school.name_headmaster.length.max"
     * )
     * @Assert\Regex(
     *     pattern="/^[a-zA-Z\p{L}-\s]+$/u",
     *     message="common.human_name.regex"
     * )
     */
    protected $nameHeadmaster;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     *
     * @CustomAssert\IsPhoneNumberConstraint
     */
    protected $phoneNumberHeadmaster;

    /**
     * @ORM\Column(type="string", length=60, nullable=true)
     *
     * @Assert\Email(
     *      message="common.email.valid",
     *      checkMX=true
     * )
     */
    protected $emailHeadmaster;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     *
     * @Assert\Length(
     *      min=2,
     *      max=500,
     *      minMessage="school.name_contact.length.min",
     *      maxMessage="school.name_contact.length.max"
     * )
     * @Assert\Regex(
     *     pattern="/^[a-zA-Z\p{L}-\s]+$/u",
     *     message="common.human_name.regex"
     * )
     */
    protected $nameContact;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     *
     * @CustomAssert\IsPhoneNumberConstraint
     */
    protected $phoneNumberContact;

    /**
     * @ORM\Column(type="string", length=60, nullable=true)
     *
     * @Assert\Email(
     *      message="common.email.valid",
     *      checkMX=true
     * )
     */
    protected $emailContact;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->employees       = new ArrayCollection;
        $this->vendingMachines = new ArrayCollection;
        $this->students        = new ArrayCollection;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return School
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
     * Set address
     *
     * @param string $address
     * @return School
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string 
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set studentsQuantity
     *
     * @param integer $studentsQuantity
     * @return School
     */
    public function setStudentsQuantity($studentsQuantity)
    {
        $this->studentsQuantity = $studentsQuantity;

        return $this;
    }

    /**
     * Get studentsQuantity
     *
     * @return integer
     */
    public function getStudentsQuantity()
    {
        return $this->studentsQuantity;
    }

    /**
     * Set phoneNumberSchool
     *
     * @param string $phoneNumberSchool
     * @return School
     */
    public function setPhoneNumberSchool($phoneNumberSchool)
    {
        $this->phoneNumberSchool = $phoneNumberSchool;

        return $this;
    }

    /**
     * Get phoneNumberSchool
     *
     * @return string 
     */
    public function getPhoneNumberSchool()
    {
        return $this->phoneNumberSchool;
    }

    /**
     * Set emailSchool
     *
     * @param string $emailSchool
     * @return School
     */
    public function setEmailSchool($emailSchool)
    {
        $this->emailSchool = $emailSchool;

        return $this;
    }

    /**
     * Get emailSchool
     *
     * @return string 
     */
    public function getEmailSchool()
    {
        return $this->emailSchool;
    }

    /**
     * Set nameHeadmaster
     *
     * @param string $nameHeadmaster
     * @return School
     */
    public function setNameHeadmaster($nameHeadmaster)
    {
        $this->nameHeadmaster = $nameHeadmaster;

        return $this;
    }

    /**
     * Get nameHeadmaster
     *
     * @return string 
     */
    public function getNameHeadmaster()
    {
        return $this->nameHeadmaster;
    }

    /**
     * Set phoneNumberHeadmaster
     *
     * @param string $phoneNumberHeadmaster
     * @return School
     */
    public function setPhoneNumberHeadmaster($phoneNumberHeadmaster)
    {
        $this->phoneNumberHeadmaster = $phoneNumberHeadmaster;

        return $this;
    }

    /**
     * Get phoneNumberHeadmaster
     *
     * @return string 
     */
    public function getPhoneNumberHeadmaster()
    {
        return $this->phoneNumberHeadmaster;
    }

    /**
     * Set emailHeadmaster
     *
     * @param string $emailHeadmaster
     * @return School
     */
    public function setEmailHeadmaster($emailHeadmaster)
    {
        $this->emailHeadmaster = $emailHeadmaster;

        return $this;
    }

    /**
     * Get emailHeadmaster
     *
     * @return string 
     */
    public function getEmailHeadmaster()
    {
        return $this->emailHeadmaster;
    }

    /**
     * Set nameContact
     *
     * @param string $nameContact
     * @return School
     */
    public function setNameContact($nameContact)
    {
        $this->nameContact = $nameContact;

        return $this;
    }

    /**
     * Get nameContact
     *
     * @return string 
     */
    public function getNameContact()
    {
        return $this->nameContact;
    }

    /**
     * Set phoneNumberContact
     *
     * @param string $phoneNumberContact
     * @return School
     */
    public function setPhoneNumberContact($phoneNumberContact)
    {
        $this->phoneNumberContact = $phoneNumberContact;

        return $this;
    }

    /**
     * Get phoneNumberContact
     *
     * @return string 
     */
    public function getPhoneNumberContact()
    {
        return $this->phoneNumberContact;
    }

    /**
     * Set emailContact
     *
     * @param string $emailContact
     * @return School
     */
    public function setEmailContact($emailContact)
    {
        $this->emailContact = $emailContact;

        return $this;
    }

    /**
     * Get emailContact
     *
     * @return string 
     */
    public function getEmailContact()
    {
        return $this->emailContact;
    }

    /**
     * Add employees
     *
     * @param \AppBundle\Entity\Employee\Employee $employee
     * @return School
     */
    public function addEmployee(\AppBundle\Entity\Employee\Employee $employee)
    {
        $this->employees[] = $employee;

        return $this;
    }

    /**
     * Remove employees
     *
     * @param \AppBundle\Entity\Employee\Employee $employees
     */
    public function removeEmployee(\AppBundle\Entity\Employee\Employee $employees)
    {
        $this->employees->removeElement($employees);
    }

    /**
     * Get employees
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEmployees()
    {
        return $this->employees;
    }

    /**
     * Set settlement
     *
     * @param \AppBundle\Entity\Settlement\Settlement $settlement
     * @return School
     */
    public function setSettlement(\AppBundle\Entity\Settlement\Settlement $settlement = null)
    {
        $this->settlement = $settlement;

        return $this;
    }

    /**
     * Get settlement
     *
     * @return \AppBundle\Entity\Settlement\Settlement
     */
    public function getSettlement()
    {
        return $this->settlement;
    }

    /**
     * Add vendingMachines
     *
     * @param \AppBundle\Entity\VendingMachine\VendingMachine $vendingMachine
     * @return School
     */
    public function addVendingMachine(\AppBundle\Entity\VendingMachine\VendingMachine $vendingMachine)
    {
        $vendingMachine->setSchool($this);
        $this->vendingMachines[] = $vendingMachine;

        return $this;
    }

    /**
     * Remove vendingMachines
     *
     * @param \AppBundle\Entity\VendingMachine\VendingMachine $vendingMachines
     */
    public function removeVendingMachine(\AppBundle\Entity\VendingMachine\VendingMachine $vendingMachines)
    {
        $this->vendingMachines->removeElement($vendingMachines);
    }

    /**
     * Get vendingMachines
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getVendingMachines()
    {
        return $this->vendingMachines;
    }

    /**
     * Add student
     *
     * @param \AppBundle\Entity\Student\Student $student
     * @return School
     */
    public function addStudent(\AppBundle\Entity\Student\Student $student)
    {
        $student->setSchool($this);
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
}