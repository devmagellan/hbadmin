<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelOrganic;

/**
 * CoursePriceBlockSetting
 *
 * @ORM\Table(name="sale_funnel_organic_price_block_setting")
 * @ORM\Entity()
 */
class SaleFunnelOrganicPriceBlockSetting
{
    const TYPE_SUBSCRIPTION_ONCE = 'ONCE';
    const TYPE_SUBSCRIPTION_MONTHLY = 'MONTHLY';

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
     * @ORM\Column(name="title", type="string", nullable=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="subscription_type", type="string")
     */
    private $subscriptionType;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float", nullable=false)
     */
    private $price;

    /**
     * @var SalesFunnelOrganic
     *
     * @ORM\ManyToOne(targetEntity="HB\AdminBundle\Entity\SaleFunnel\SalesFunnelOrganic")
     * @ORM\JoinColumn(name="sale_funnel_organic_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $funnel;

    /**
     * @var CoursePriceBlock
     *
     * @ORM\ManyToOne(targetEntity="HB\AdminBundle\Entity\CoursePriceBlock")
     * @ORM\JoinColumn(name="course_price_block_id", referencedColumnName="id")
     */
    private $coursePriceBlock;

    /**
     * @var ArrayCollection | SaleFunnelOrganicPriceBlockThesis[]
     *
     * @ORM\OneToMany(targetEntity="HB\AdminBundle\Entity\SaleFunnelOrganicPriceBlockThesis", mappedBy="saleFunnelOrganicPriceBlockSetting", cascade={"all"})
     */
    private $theses;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="HB\AdminBundle\Entity\Course")
     * @ORM\JoinTable(name="price_block_settings_courses",
     *      joinColumns={@ORM\JoinColumn(name="price_block_setting_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="course_id", referencedColumnName="id", onDelete="CASCADE")}
     *      )
     */
    private $courses;

    /**
     * CoursePriceBlockSetting constructor.
     *
     * @param SalesFunnelOrganic $funnel
     * @param CoursePriceBlock   $coursePriceBlock
     */
    public function __construct(SalesFunnelOrganic $funnel, CoursePriceBlock $coursePriceBlock)
    {
        $this->subscriptionType = self::TYPE_SUBSCRIPTION_ONCE;
        $this->price = 0;
        $this->funnel = $funnel;
        $this->coursePriceBlock = $coursePriceBlock;
        $this->theses = new ArrayCollection();
        $this->courses = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getSubscriptionType(): string
    {
        return $this->subscriptionType;
    }

    /**
     * @param string $subscriptionType
     */
    public function setSubscriptionType(string $subscriptionType): void
    {
        $this->subscriptionType = $subscriptionType;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @param float $price
     */
    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    /**
     * @return SalesFunnelOrganic
     */
    public function getFunnel(): SalesFunnelOrganic
    {
        return $this->funnel;
    }

    /**
     * @return CoursePriceBlock
     */
    public function getCoursePriceBlock(): CoursePriceBlock
    {
        return $this->coursePriceBlock;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->title;
    }


    /**
     * Add thesis.
     *
     * @param SaleFunnelOrganicPriceBlockThesis $thesis
     *
     * @return SaleFunnelOrganicPriceBlockSetting
     */
    public function addThesis(SaleFunnelOrganicPriceBlockThesis $thesis)
    {
        $this->theses[] = $thesis;

        return $this;
    }

    /**
     * Remove thesis.
     *
     * @param SaleFunnelOrganicPriceBlockThesis $thesis
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeThesis(SaleFunnelOrganicPriceBlockThesis $thesis)
    {
        return $this->theses->removeElement($thesis);
    }

    /**
     * Get thesises.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTheses()
    {
        return $this->theses;
    }

    /**
     * Add course.
     *
     * @param Course $course
     */
    public function addCourse(Course $course): void
    {
        $this->courses[] = $course;
    }

    /**
     * @param Course $course
     */
    public function removeCourse(Course $course)
    {
        $this->courses->removeElement($course);
    }

    /**
     * Remove course.
     *
     * @param Course $course
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeFunnel(Course $course)
    {
        return $this->courses->removeElement($course);
    }

    /**
     * Get funnels.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCourses()
    {
        return $this->courses;
    }
}
