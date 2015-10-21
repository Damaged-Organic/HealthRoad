<?php
// AppBundle/Entity/Student/Student.php
namespace AppBundle\Entity\Student;

use Symfony\Component\Validator\Constraints as Assert,
    Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection;

use AppBundle\Entity\Utility\Traits\DoctrineMapping\IdMapperTrait,
    AppBundle\Validator\Constraints as CustomAssert;

/**
 * @ORM\Table(name="students")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Student\Repository\StudentRepository")
 */
class Student
{
    use IdMapperTrait;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Employee\Employee", inversedBy="students")
     * @ORM\JoinColumn(name="employee_id", referencedColumnName="id", nullable=false)
     */
    protected $employee;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\School\School", inversedBy="students")
     * @ORM\JoinColumn(name="school_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $school;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Customer\Customer", inversedBy="students")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $customer;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\NfcTag\NfcTag", mappedBy="student")
     */
    protected $nfcTag;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Product\Product", inversedBy="students")
     * @ORM\JoinTable(name="students_products")
     */
    protected $products;

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
     *     pattern = "/^[a-zA-Z\p{L}-]+$/u",
     *     message = "common.human_name.regex"
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
     *     pattern = "/^[a-zA-Z\p{L}-]+$/u",
     *     message = "common.human_name.regex"
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
     *     pattern = "/^[a-zA-Z\p{L}-]+$/u",
     *     message = "common.human_name.regex"
     * )
     */
    protected $patronymic;

    /**
     * @ORM\Column(type="string", length=6)
     *
     * @Assert\Choice(
     *      choices = {"male", "female"},
     *      message = "student.gender.choice"
     * )
     */
    protected $gender;

    /**
     * @ORM\Column(type="date")
     *
     * @Assert\Date(
     *      message = "common.date.valid"
     * )
     */
    protected $dateOfBirth;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     *
     * @CustomAssert\IsPriceConstraint
     */
    protected $totalLimit;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     *
     * @CustomAssert\IsPriceConstraint
     */
    protected $dailyLimit;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->products = new ArrayCollection;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Student
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
     * @return Student
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
     * @return Student
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
     * Set gender
     *
     * @param string $gender
     * @return Student
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return string 
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set dateOfBirth
     *
     * @param \DateTime $dateOfBirth
     * @return Student
     */
    public function setDateOfBirth($dateOfBirth)
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }

    /**
     * Get dateOfBirth
     *
     * @return \DateTime 
     */
    public function getDateOfBirth()
    {
        return $this->dateOfBirth;
    }

    /**
     * Set totalLimit
     *
     * @param string $totalLimit
     * @return Student
     */
    public function setTotalLimit($totalLimit)
    {
        $this->totalLimit = $totalLimit;

        return $this;
    }

    /**
     * Get totalLimit
     *
     * @return string
     */
    public function getTotalLimit()
    {
        return $this->totalLimit;
    }

    /**
     * Set dailyLimit
     *
     * @param string $dailyLimit
     * @return Student
     */
    public function setDailyLimit($dailyLimit)
    {
        $this->dailyLimit = $dailyLimit;

        return $this;
    }

    /**
     * Get dailyLimit
     *
     * @return string 
     */
    public function getDailyLimit()
    {
        return $this->dailyLimit;
    }

    /**
     * Set employee
     *
     * @param \AppBundle\Entity\Employee\Employee $employee
     * @return Student
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
     * Set school
     *
     * @param \AppBundle\Entity\School\School $school
     * @return Student
     */
    public function setSchool(\AppBundle\Entity\School\School $school = null)
    {
        $this->school = $school;

        return $this;
    }

    /**
     * Get school
     *
     * @return \AppBundle\Entity\School\School 
     */
    public function getSchool()
    {
        return $this->school;
    }

    /**
     * Set customer
     *
     * @param \AppBundle\Entity\Customer\Customer $customer
     * @return Student
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

    /**
     * Set nfcTag
     *
     * @param \AppBundle\Entity\NfcTag\NfcTag $nfcTag
     * @return Student
     */
    public function setNfcTag(\AppBundle\Entity\NfcTag\NfcTag $nfcTag = null)
    {
        $this->nfcTag = $nfcTag;

        return $this;
    }

    /**
     * Get nfcTag
     *
     * @return \AppBundle\Entity\NfcTag\NfcTag 
     */
    public function getNfcTag()
    {
        return $this->nfcTag;
    }

    /**
     * Add product
     *
     * @param \AppBundle\Entity\Product\Product $product
     * @return Student
     */
    public function addProduct(\AppBundle\Entity\Product\Product $product)
    {
        $product->addStudent($this);
        $this->products[] = $product;

        return $this;
    }

    /**
     * Remove products
     *
     * @param \AppBundle\Entity\Product\Product $products
     */
    public function removeProduct(\AppBundle\Entity\Product\Product $products)
    {
        $this->products->removeElement($products);
    }

    /**
     * Get products
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * Shortcut to get products if possible
     *
     * @return \Doctrine\Common\Collections\Collection|null
     */
    public function getRestrictedProducts()
    {
        if( $this->getNfcTag() )
        {
            if( $this->getNfcTag()->getVendingMachine() )
            {
                if( $this->getNfcTag()->getVendingMachine()->getProductVendingGroup() )
                {
                    return $this->getNfcTag()->getVendingMachine()->getProductVendingGroup()->getProducts();
                }
            }
        }

        return NULL;
    }

    public function getFullName()
    {
        if( !$this->patronymic && !$this->name && !$this->surname )
            return NULL;

        return "{$this->surname} {$this->name} {$this->patronymic}";
    }
}