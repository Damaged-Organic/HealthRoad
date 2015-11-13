<?php
// AppBundle/Entity/Website/Contact/Contact.php
namespace AppBundle\Entity\Website\Contact;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Doctrine\ORM\Mapping as ORM;

use AppBundle\Entity\Utility\Traits\DoctrineMapping\IdMapperTrait;

/**
 * @ORM\Table(name="website_contacts")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Website\Contact\Repository\ContactRepository")
 *
 * @UniqueEntity(fields="alias")
 */
class Contact
{
    use IdMapperTrait;

    /**
     * @ORM\Column(type="string", length=250, unique=true)
     */
    protected $alias;

    /**
     * @ORM\Column(type="string", length=250, nullable=true)
     */
    protected $title;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    protected $position;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $schedule;

    /**
     * @ORM\Column(type="string", length=20)
     */
    protected $phoneNumber;

    /**
     * @ORM\Column(type="string", length=60, nullable=true)
     */
    protected $email;

    /**
     * @ORM\Column(type="string", length=250, nullable=true)
     */
    protected $mail;

    /**
     * To string
     */
    public function __toString()
    {
        return ( $this->getAlias() ) ?: '';
    }

    /**
     * Set alias
     *
     * @param string $alias
     * @return Contact
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * Get alias
     *
     * @return string 
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Contact
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set position
     *
     * @param string $position
     * @return Contact
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return string
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set schedule
     *
     * @param string $schedule
     * @return Contact
     */
    public function setSchedule($schedule)
    {
        $this->schedule = $schedule;

        return $this;
    }

    /**
     * Get schedule
     *
     * @return string 
     */
    public function getSchedule()
    {
        return $this->schedule;
    }

    /**
     * Set phoneNumber
     *
     * @param string $phoneNumber
     * @return Contact
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
     * Set email
     *
     * @param string $email
     * @return Contact
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
     * Set mail
     *
     * @param string $mail
     * @return Contact
     */
    public function setMail($mail)
    {
        $this->mail = $mail;

        return $this;
    }

    /**
     * Get mail
     *
     * @return string 
     */
    public function getMail()
    {
        return $this->mail;
    }

    static public function headerize(array $inputContacts)
    {
        $outputContacts = [];

        foreach($inputContacts as $contact)
        {
            $outputContacts[$contact->getAlias()] = $contact;
        }

        return $outputContacts;
    }
}