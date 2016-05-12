<?php
// src/AppBundle/Entity/PurchaseService/PurchaseService.php
namespace AppBundle\Entity\PurchaseService;

use Doctrine\ORM\Mapping as ORM;

use AppBundle\Entity\Utility\Traits\DoctrineMapping\IdMapperTrait;

/**
 * @ORM\Table(name="purchases_service")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\PurchaseService\Repository\PurchaseServiceRepository")
 */

class PurchaseService
{
    use IdMapperTrait;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\NfcTag\NfcTag", inversedBy="purchasesService")
     * @ORM\JoinColumn(name="nfc_tag_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $nfcTag;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Student\Student", inversedBy="purchasesService")
     * @ORM\JoinColumn(name="student_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $student;

    /**
     * @ORM\Column(type="string", length=500)
     */
    protected $item;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    protected $price;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $purchasedAt;

    public function getSearchProperties()
    {
        $searchProperties = [
            $this->getItem(),
        ];

        if( $this->getPurchasedAt() ) {
            $searchProperties[] = $this->getPurchasedAt()->format('Y-m-d, H:i:s');
        }

        if( $this->getNfcTag() ) {
            $searchProperties[] = $this->getNfcTag()->getNumber();
        }

        if( $this->getStudent() ) {
            $searchProperties[] = $this->getStudent()->getName();
            $searchProperties[] = $this->getStudent()->getSurname();
            $searchProperties[] = $this->getStudent()->getPatronymic();
        }

        return $searchProperties;
    }

    /**
     * Set item
     *
     * @param string $item
     * @return PurchaseService
     */
    public function setItem($item)
    {
        $this->item = $item;

        return $this;
    }

    /**
     * Get item
     *
     * @return string
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * Set price
     *
     * @param string $price
     * @return PurchaseService
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set purchasedAt
     *
     * @param \DateTime $purchasedAt
     * @return PurchaseService
     */
    public function setPurchasedAt($purchasedAt)
    {
        $this->purchasedAt = $purchasedAt;

        return $this;
    }

    /**
     * Get purchasedAt
     *
     * @return \DateTime
     */
    public function getPurchasedAt()
    {
        return $this->purchasedAt;
    }

    /**
     * Set nfcTag
     *
     * @param \AppBundle\Entity\NfcTag\NfcTag $nfcTag
     * @return PurchaseService
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
     * Set student
     *
     * @param \AppBundle\Entity\Student\Student $student
     * @return PurchaseService
     */
    public function setStudent(\AppBundle\Entity\Student\Student $student = null)
    {
        $this->student = $student;

        return $this;
    }

    /**
     * Get student
     *
     * @return \AppBundle\Entity\Student\Student
     */
    public function getStudent()
    {
        return $this->student;
    }
}
