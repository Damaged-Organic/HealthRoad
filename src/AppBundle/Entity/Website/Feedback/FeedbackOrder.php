<?php
// AppBundle/Entity/Website/Feedback/FeedbackOrder.php
namespace AppBundle\Entity\Website\Feedback;

use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;

use AppBundle\Entity\Utility\Traits\DoctrineMapping\IdMapperTrait,
    AppBundle\Validator\Constraints as CustomAssert;

/**
 * @ORM\Table(name="website_feedback_order")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Website\Feedback\Repository\FeedbackOrderRepository")
 */
class FeedbackOrder
{
    use IdMapperTrait;

    /**
     * @ORM\Column(type="string", length=250)
     *
     * @Assert\NotBlank(
     *      message="website.feedback.name.not_blank"
     * )
     * @Assert\Length(
     *      min=2,
     *      max=250,
     *      minMessage="website.feedback.name.length.min",
     *      maxMessage="website.feedback.name.length.max"
     * )
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=60)
     *
     * @Assert\NotBlank(
     *      message="website.feedback.email.not_blank"
     * )
     * @Assert\Email(
     *      message="website.feedback.email.valid",
     *      checkMX=true
     * )
     */
    protected $email;

    /**
     * @ORM\Column(type="string", length=20)
     *
     * @Assert\NotBlank(
     *      message="website.feedback.phone_number.not_blank"
     * )
     * @CustomAssert\IsPhoneNumberConstraint
     */
    protected $phoneNumber;

    /**
     * @ORM\Column(type="text")
     *
     * @Assert\NotBlank(
     *      message="website.feedback.message.not_blank"
     * )
     * @Assert\Length(
     *      min=5,
     *      max=1500,
     *      minMessage="website.feedback.message.length.min",
     *      maxMessage="website.feedback.message.length.max"
     * )
     */
    protected $message;

    /**
     * Set name
     *
     * @param string $name
     * @return FeedbackOrder
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
     * @return FeedbackOrder
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
     * @return FeedbackOrder
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
     * @return FeedbackOrder
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