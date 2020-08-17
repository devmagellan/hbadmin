<?php

namespace HB\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CustomerAction
 *
 * @ORM\Table(name="customer_action")
 * @ORM\Entity(repositoryClass="HB\AdminBundle\Repository\CustomerActionRepository")
 */
class CustomerAction
{
    const STATUS_NEW_MODERATE = 'NEW_MODERATE';

    const TYPE_COURSE = 'COURSE';
    const TYPE_LESSON = 'LESSON';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255)
    */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_viewed", type="boolean")
    */
    private $isViewed;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateCreatedAt", type="datetime")
     */
    private $dateCreatedAt;

    /**
     * @var Customer
     *
     * @ORM\ManyToOne(targetEntity="HB\AdminBundle\Entity\Customer")
     * @ORM\JoinColumn(name="id_customer", referencedColumnName="id")
    */
    private $customer;


    public function __construct()
    {
        $this->setDateCreatedAt(new \DateTime("now"));
        $this->setIsViewed(false);
    }


    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param Customer $customer
     */
    public function setCustomer(Customer $customer)
    {
        $this->customer = $customer;
    }

    /**
     * @return bool
     */
    public function isViewed()
    {
        return $this->isViewed;
    }

    /**
     * @param bool $isViewed
     */
    public function setIsViewed($isViewed)
    {
        $this->isViewed = $isViewed;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return CustomerAction
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set dateCreatedAt.
     *
     * @param \DateTime $dateCreatedAt
     *
     * @return CustomerAction
     */
    public function setDateCreatedAt($dateCreatedAt)
    {
        $this->dateCreatedAt = $dateCreatedAt;

        return $this;
    }

    /**
     * Get dateCreatedAt.
     *
     * @return \DateTime
     */
    public function getDateCreatedAt()
    {
        return $this->dateCreatedAt;
    }
}
