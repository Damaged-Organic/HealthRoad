<?php
// AppBundle/Entity/Website/Feedback/Feedback.php
namespace AppBundle\Entity\Website\Feedback;

use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;

use AppBundle\Entity\Utility\Traits\DoctrineMapping\IdMapperTrait,
    AppBundle\Validator\Constraints as CustomAssert;

/**
 * @ORM\Table(name="website_feedbacks")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Website\Feedback\Repository\FeedbackRepository")
 */
// TODO: Implement type interface
class Feedback
{
    use IdMapperTrait;

    /**
     * @ORM\Column(type="string", length=50)
     */
    protected $type;

    /**
     * @ORM\Column(type="string", length=250)
     *
     * @Assert\Length(
     *      min=2,
     *      max=250,
     *      minMessage="feedback.name.length_min",
     *      maxMessage="feedback.name.length_max"
     * )
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=60)
     *
     * @Assert\NotBlank(
     *      message="feedback.email.not_blank"
     * )
     * @Assert\Email(
     *      message="feedback.email.valid"
     * )
     */
    protected $email;

    /**
     * @ORM\Column(type="string", length=20)
     *
     * @CustomAssert\IsPhoneNumberConstraint
     */
    // TODO: Different message
    protected $phoneNumber;

    /**
     * @ORM\Column(type="text")
     *
     * @Assert\NotBlank(
     *      message="feedback.message.not_blank"
     * )
     * @Assert\Length(
     *      min=5,
     *      max=1500,
     *      minMessage="feedback.message.length_min",
     *      maxMessage="feedback.message.length_max"
     * )
     */
    protected $message;

    /**
     * Set type
     *
     * @param string $type
     * @return Feedback
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Feedback
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
     * Set email
     *
     * @param string $email
     * @return Feedback
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
     * @return Feedback
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
     * Set message
     *
     * @param string $message
     * @return Feedback
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string 
     */
    public function getMessage()
    {
        return $this->message;
    }
}