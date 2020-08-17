<?php

declare(strict_types = 1);

namespace HB\AdminBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use HB\AdminBundle\Entity\Embedded\CourseInfo;
use HB\AdminBundle\Entity\Embedded\Integrations;
use HB\AdminBundle\Entity\Embedded\StopLessons;
use HB\AdminBundle\Entity\SaleFunnel\CourseAccessInterface;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelAuthorClub;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelBrokenBasket;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelCrossSale;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelDownSell;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelEducational;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelHotLeads;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelLayerCake;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelLeadCollection;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelMiniCourse;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelOneTimeOffer;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelOrganic;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelPartner;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelPostSale;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelWalkerStart;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelWebinar;
use HB\AdminBundle\Entity\Types\CourseStatusType;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Course
 *
 * @ORM\Table(name="course")
 * @ORM\Entity(repositoryClass="HB\AdminBundle\Repository\CourseRepository")
 */
class Course implements CourseAccessInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var CourseInfo
     *
     * @Assert\Valid
     *
     * @ORM\Embedded(class="HB\AdminBundle\Entity\Embedded\CourseInfo", columnPrefix="info_")
     */
    private $info;

    /**
     * @var StopLessons
     *
     * @Assert\Valid
     *
     * @ORM\Embedded(class="HB\AdminBundle\Entity\Embedded\StopLessons", columnPrefix="stop_lessons_")
     */
    private $stopLessons;

    /**
     * @var Integrations
     *
     * @ORM\Embedded(class="HB\AdminBundle\Entity\Embedded\Integrations", columnPrefix="integr_")
     */
    private $integrations;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=100, nullable=false)
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateCreatedAt", type="datetime")
     */
    private $dateCreatedAt;

    /**
     * @Assert\NotBlank()
     *
     * @ORM\ManyToOne(targetEntity="HB\AdminBundle\Entity\Customer", inversedBy="courses")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $owner;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="HB\AdminBundle\Entity\LessonSection", mappedBy="course", cascade={"persist", "remove"})
     */
    private $lessonSections;

    /**
     * @var SalesFunnelOrganic | null
     *
     * @ORM\OneToOne(targetEntity="HB\AdminBundle\Entity\SaleFunnel\SalesFunnelOrganic", mappedBy="course", cascade={"all", "remove"})
     * @ORM\JoinColumn(name="sale_funnel_organic_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private $salesFunnelOrganic;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="HB\AdminBundle\Entity\SaleFunnel\SalesFunnelLeadCollection", mappedBy="course", cascade={"all"})
     */
    private $saleFunnelLeadCollections;

    /**
     * @var SalesFunnelBrokenBasket | null
     *
     * @ORM\OneToOne(targetEntity="HB\AdminBundle\Entity\SaleFunnel\SalesFunnelBrokenBasket", mappedBy="course", cascade={"all"})
     * @ORM\JoinColumn(name="sale_funnel_broken_basket_id", nullable=true, onDelete="CASCADE")
     */
    private $salesFunnelBrokenBasket;

    /**
     * @var SalesFunnelPostSale | null
     *
     * @ORM\OneToOne(targetEntity="HB\AdminBundle\Entity\SaleFunnel\SalesFunnelPostSale", mappedBy="course", cascade={"all"})
     * @ORM\JoinColumn(name="sale_funnel_post_sale_id", nullable=true, onDelete="CASCADE")
     */
    private $salesFunnelPostSale;

    /**
     * @var SalesFunnelCrossSale | null
     *
     * @ORM\OneToOne(targetEntity="HB\AdminBundle\Entity\SaleFunnel\SalesFunnelCrossSale", mappedBy="course", cascade={"all"})
     * @ORM\JoinColumn(name="sale_funnel_cross_sale_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private $salesFunnelCrossSale;

    /**
     * @var SalesFunnelHotLeads | null
     *
     * @ORM\OneToOne(targetEntity="HB\AdminBundle\Entity\SaleFunnel\SalesFunnelHotLeads", mappedBy="course", cascade={"all"})
     * @ORM\JoinColumn(name="sale_funnel_hot_leads_id", nullable=true, onDelete="SET NULL")
     */
    private $salesFunnelHotLeads;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="HB\AdminBundle\Entity\SaleFunnel\SalesFunnelLayerCake", mappedBy="course", cascade={"all"})
     */
    private $saleFunnelLayerCakes;

    /**
     * @var SalesFunnelMiniCourse | null
     *
     * @ORM\OneToOne(targetEntity="HB\AdminBundle\Entity\SaleFunnel\SalesFunnelMiniCourse", mappedBy="course", cascade={"all"})
     * @ORM\JoinColumn(name="sale_funnel_mini_course_id", nullable=true, onDelete="SET NULL")
     */
    private $salesFunnelMiniCourse;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="HB\AdminBundle\Entity\SaleFunnel\SalesFunnelWebinar", mappedBy="course", cascade={"all"})
     */
    private $salesFunnelWebinar;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="HB\AdminBundle\Entity\SaleFunnel\SalesFunnelDownSell", mappedBy="course", cascade={"all"})
     */
    private $saleFunnelDownSells;

    /**
     * @var SalesFunnelWalkerStart | null
     *
     * @ORM\OneToOne(targetEntity="HB\AdminBundle\Entity\SaleFunnel\SalesFunnelWalkerStart", mappedBy="course", cascade={"all"})
     * @ORM\JoinColumn(name="sale_funnel_walker_start_id", nullable=true, onDelete="SET NULL")
     */
    private $salesFunnelWalkerStart;

    /**
     * @var SalesFunnelAuthorClub | null
     *
     * @ORM\OneToOne(targetEntity="HB\AdminBundle\Entity\SaleFunnel\SalesFunnelAuthorClub", mappedBy="course", cascade={"all"})
     * @ORM\JoinColumn(name="sale_funnel_author_club_id", nullable=true, onDelete="SET NULL")
     */
    private $salesFunnelAuthorClub;

    /**
     * @var SalesFunnelEducational | null
     *
     * @ORM\OneToOne(targetEntity="HB\AdminBundle\Entity\SaleFunnel\SalesFunnelEducational", mappedBy="course", cascade={"all"})
     * @ORM\JoinColumn(name="sale_funnel_educational_id", nullable=true, onDelete="SET NULL")
     */
    private $salesFunnelEducational;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="HB\AdminBundle\Entity\SaleFunnel\SalesFunnelOneTimeOffer", mappedBy="course", cascade={"all"})
     */
    private $saleFunnelOneTimeOffer;

    /**
     * @var bool
     *
     * @ORM\Column(name="sandbox", type="boolean")
     */
    private $sandbox;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="HB\AdminBundle\Entity\SaleFunnel\SalesFunnelPartner", mappedBy="course", cascade={"all"})
     */
    private $saleFunnelPartners;

    /**
     * @var bool
     *
     * @ORM\Column(name="had_moderate_request", type="boolean")
     */
    private $hadPublishRequest = false;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="HB\AdminBundle\Entity\ActionLog", mappedBy="course", cascade={"all"})
     */
    private $logs;

    /**
     * @var string | null
     *
     * @ORM\Column(name="link_online_school", type="string", nullable=true)
     */
    private $linkOnlineSchool;

    /**
     * @var string | null
     *
     * @ORM\Column(name="link_payment_page", type="string", nullable=true)
     */
    private $linkPaymentPage;

    /**
     * @var int | null
     *
     * @ORM\Column(name="teachable_id", type="integer", nullable=true)
     */
    private $teachableId;

    /**
     * Course constructor.
     * @param bool $sandbox
     */
    public function __construct(bool $sandbox = false)
    {
        $this->info = new CourseInfo();
        $this->integrations = new Integrations();
        $this->stopLessons = new StopLessons();
        $this->setDateCreatedAt(new \DateTime("now"));
        $this->setStatus(new CourseStatusType(CourseStatusType::STATUS_IN_PROGRESS));
        $this->lessonSections = new ArrayCollection();
        $this->saleFunnelLeadCollections = new ArrayCollection();
        $this->saleFunnelLayerCakes = new ArrayCollection();
        $this->saleFunnelDownSells = new ArrayCollection();
        $this->salesFunnelWebinar = new ArrayCollection();
        $this->saleFunnelOneTimeOffer = new ArrayCollection();
        $this->saleFunnelPartners = new ArrayCollection();
        $this->sandbox = $sandbox;
    }

    /**
     * @return CourseStatusType
     */
    public function getStatus(): CourseStatusType
    {
        return new CourseStatusType($this->status);
    }

    /**
     * @param CourseStatusType $status
     */
    public function setStatus(CourseStatusType $status): void
    {
        $this->status = $status->getValue();
    }

    /**
     * @return Customer
     */
    public function getOwner(): ?Customer
    {
        return $this->owner;
    }

    /**
     * @param Customer $owner
     */
    public function setOwner(Customer $owner): void
    {
        $this->owner = $owner;
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param \DateTime $dateCreatedAt
     */
    public function setDateCreatedAt($dateCreatedAt): void
    {
        $this->dateCreatedAt = $dateCreatedAt;
    }

    /**
     * @return \DateTime
     */
    public function getDateCreatedAt(): \DateTime
    {
        return $this->dateCreatedAt;
    }

    /**
     * @return CourseInfo
     */
    public function getInfo(): ?CourseInfo
    {
        return $this->info;
    }

    /**
     * @param CourseInfo $info
     */
    public function setInfo(CourseInfo $info): void
    {
        $this->info = $info;
    }

    /**
     * @return Integrations
     */
    public function getIntegrations(): Integrations
    {
        return $this->integrations;
    }

    /**
     * @param Integrations $integrations
     */
    public function setIntegrations(Integrations $integrations): void
    {
        $this->integrations = $integrations;
    }

    /**
     * @return ArrayCollection | LessonSection[]
     */
    public function getLessonSections()
    {
        return $this->lessonSections;
    }

    /**
     * @param ArrayCollection $lessonSections
     */
    public function setLessonSections(ArrayCollection $lessonSections): void
    {
        $this->lessonSections = $lessonSections;
    }

    /**
     * @param LessonSection $section
     */
    public function addLessonSection(LessonSection $section)
    {
        $this->lessonSections->add($section);
    }

    /**
     * @return SalesFunnelOrganic|null
     */
    public function getSalesFunnelOrganic(): ?SalesFunnelOrganic
    {
        return $this->salesFunnelOrganic;
    }

    /**
     * @param SalesFunnelOrganic|null $salesFunnelOrganic
     */
    public function setSalesFunnelOrganic(?SalesFunnelOrganic $salesFunnelOrganic): void
    {
        $this->salesFunnelOrganic = $salesFunnelOrganic;
        $salesFunnelOrganic->setCourse($this);
    }

    /**
     * @return ArrayCollection
     */
    public function getSaleFunnelLeadCollections()
    {
        return $this->saleFunnelLeadCollections;
    }

    /**
     * @param SalesFunnelLeadCollection $salesFunnelLeadCollection
     */
    public function addFunnelLeadCollection(SalesFunnelLeadCollection $salesFunnelLeadCollection)
    {
        $this->saleFunnelLeadCollections[] = $salesFunnelLeadCollection;
    }

    /**
     * @param SalesFunnelLeadCollection $salesFunnelLeadCollection
     */
    public function removeFunnelLeadCollection(SalesFunnelLeadCollection $salesFunnelLeadCollection)
    {
        $this->saleFunnelLeadCollections->removeElement($salesFunnelLeadCollection);
    }

    /**
     * @return SalesFunnelBrokenBasket|null
     */
    public function getSalesFunnelBrokenBasket(): ?SalesFunnelBrokenBasket
    {
        return $this->salesFunnelBrokenBasket;
    }

    /**
     * @param SalesFunnelBrokenBasket|null $salesFunnelBrokenBasket
     */
    public function setSalesFunnelBrokenBasket(?SalesFunnelBrokenBasket $salesFunnelBrokenBasket): void
    {
        $this->salesFunnelBrokenBasket = $salesFunnelBrokenBasket;
    }

    /**
     * @return SalesFunnelPostSale|null
     */
    public function getSalesFunnelPostSale(): ?SalesFunnelPostSale
    {
        return $this->salesFunnelPostSale;
    }

    /**
     * @param SalesFunnelPostSale|null $salesFunnelPostSale
     */
    public function setSalesFunnelPostSale(?SalesFunnelPostSale $salesFunnelPostSale): void
    {
        $this->salesFunnelPostSale = $salesFunnelPostSale;
    }

    /**
     * @return SalesFunnelCrossSale|null
     */
    public function getSalesFunnelCrossSale(): ?SalesFunnelCrossSale
    {
        return $this->salesFunnelCrossSale;
    }

    /**
     * @param SalesFunnelCrossSale|null $salesFunnelCrossSale
     */
    public function setSalesFunnelCrossSale(?SalesFunnelCrossSale $salesFunnelCrossSale): void
    {
        $this->salesFunnelCrossSale = $salesFunnelCrossSale;
    }

    /**
     * @return SalesFunnelHotLeads|null
     */
    public function getSalesFunnelHotLeads(): ?SalesFunnelHotLeads
    {
        return $this->salesFunnelHotLeads;
    }

    /**
     * @param SalesFunnelHotLeads|null $salesFunnelHotLeads
     */
    public function setSalesFunnelHotLeads(?SalesFunnelHotLeads $salesFunnelHotLeads): void
    {
        $this->salesFunnelHotLeads = $salesFunnelHotLeads;
    }

    /**
     * @return ArrayCollection
     */
    public function getSaleFunnelLayerCakes()
    {
        return $this->saleFunnelLayerCakes;
    }

    /**
     * @param SalesFunnelLayerCake $salesFunnelLayerCake
     */
    public function addFunnelLayerCake(SalesFunnelLayerCake $salesFunnelLayerCake)
    {
        $this->saleFunnelLayerCakes[] = $salesFunnelLayerCake;
    }

    /**
     * @param SalesFunnelLayerCake $salesFunnelLayerCake
     */
    public function removeFunnelLayerCake(SalesFunnelLayerCake $salesFunnelLayerCake)
    {
        $this->saleFunnelLayerCakes->removeElement($salesFunnelLayerCake);
    }

    /**
     * @return SalesFunnelMiniCourse|null
     */
    public function getSalesFunnelMiniCourse(): ?SalesFunnelMiniCourse
    {
        return $this->salesFunnelMiniCourse;
    }

    /**
     * @param SalesFunnelMiniCourse|null $salesFunnelMiniCourse
     */
    public function setSalesFunnelMiniCourse(?SalesFunnelMiniCourse $salesFunnelMiniCourse): void
    {
        $this->salesFunnelMiniCourse = $salesFunnelMiniCourse;
    }


    /**
     * @return ArrayCollection
     */
    public function getSalesFunnelWebinar()
    {
        return $this->salesFunnelWebinar;
    }

    /**
     * @param SalesFunnelWebinar $funnel
     */
    public function addSalesFunnelWebinar(SalesFunnelWebinar $funnel)
    {
        $this->salesFunnelWebinar[] = $funnel;
    }

    /**
     * @param SalesFunnelWebinar $funnel
     */
    public function removeSalesFunnelWebinar(SalesFunnelWebinar $funnel)
    {
        $this->salesFunnelWebinar->removeElement($funnel);
    }

    /**
     * @param SalesFunnelDownSell $funnel
     */
    public function addSalesFunnelDownSell(SalesFunnelDownSell $funnel): void
    {
        $this->saleFunnelDownSells[] = $funnel;
    }

    /**
     * @param SalesFunnelDownSell $funnel
     */
    public function removeSalesFunnelDownSell(SalesFunnelDownSell $funnel): void
    {
        $this->saleFunnelDownSells->removeElement($funnel);
    }

    /**
     * @return ArrayCollection
     */
    public function getSalesFunnelDownSells()
    {
        return $this->saleFunnelDownSells;
    }

    /**
     * @return SalesFunnelWalkerStart|null
     */
    public function getSalesFunnelWalkerStart(): ?SalesFunnelWalkerStart
    {
        return $this->salesFunnelWalkerStart;
    }

    /**
     * @param SalesFunnelWalkerStart|null $salesFunnelWalkerStart
     */
    public function setSalesFunnelWalkerStart(?SalesFunnelWalkerStart $salesFunnelWalkerStart): void
    {
        $this->salesFunnelWalkerStart = $salesFunnelWalkerStart;
    }

    /**
     * @return SalesFunnelAuthorClub|null
     */
    public function getSalesFunnelAuthorClub(): ?SalesFunnelAuthorClub
    {
        return $this->salesFunnelAuthorClub;
    }

    /**
     * @param SalesFunnelAuthorClub|null $salesFunnelAuthorClub
     */
    public function setSalesFunnelAuthorClub(?SalesFunnelAuthorClub $salesFunnelAuthorClub): void
    {
        $this->salesFunnelAuthorClub = $salesFunnelAuthorClub;
    }

    /**
     * @return SalesFunnelEducational|null
     */
    public function getSalesFunnelEducational(): ?SalesFunnelEducational
    {
        return $this->salesFunnelEducational;
    }

    /**
     * @param SalesFunnelEducational|null $salesFunnelEducational
     */
    public function setSalesFunnelEducational(?SalesFunnelEducational $salesFunnelEducational): void
    {
        $this->salesFunnelEducational = $salesFunnelEducational;
    }

    /**
     * @return ArrayCollection
     */
    public function getSalesFunnelOneTimeOffer()
    {
        return $this->saleFunnelOneTimeOffer;
    }

    /**
     * @param SalesFunnelOneTimeOffer $funnel
     */
    public function addSalesFunnelOneTimeOffer(SalesFunnelOneTimeOffer $funnel)
    {
        $this->saleFunnelOneTimeOffer[] = $funnel;
    }

    /**
     * @param SalesFunnelOneTimeOffer $funnel
     */
    public function removeSalesFunnelOneTimeOffer(SalesFunnelOneTimeOffer $funnel)
    {
        $this->saleFunnelOneTimeOffer->removeElement($funnel);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getInfo()->getTitle();
    }

    /**
     * @return Course
     */
    public function getCourse(): Course
    {
        return $this;
    }

    /**
     * @return bool
     */
    public function isSandbox(): bool
    {
        return $this->sandbox;
    }

    /**
     * @return ArrayCollection
     */
    public function getSaleFunnelPartners()
    {
        return $this->saleFunnelPartners;
    }

    /**
     * @param SalesFunnelPartner $funnel
     */
    public function addSaleFunnelPartner(SalesFunnelPartner $funnel)
    {
        $this->saleFunnelPartners->add($funnel);
    }

    /**
     * @return StopLessons
     */
    public function getStopLessons(): ?StopLessons
    {
        return $this->stopLessons;
    }

    /**
     * @param StopLessons $stopLessons
     */
    public function setStopLessons(StopLessons $stopLessons): void
    {
        $this->stopLessons = $stopLessons;
    }

    /**
     * @return bool
     */
    public function isHadPublishRequest(): bool
    {
        return $this->hadPublishRequest;
    }

    /**
     * @param bool $hadPublishRequest
     */
    public function setHadPublishRequest(bool $hadPublishRequest): void
    {
        $this->hadPublishRequest = $hadPublishRequest;
    }

    /**
     * @return bool
     */
    public function needSaveLogs(): bool
    {
        return $this->hadPublishRequest;
    }

    /**
     * @return bool
     */
    public function hasUnpublishedLogs()
    {
        /** @var ActionLog $log */
        foreach ($this->logs as $log) {
            if (!$log->isPublished()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return string|null
     */
    public function getLinkOnlineSchool(): ?string
    {
        return $this->linkOnlineSchool;
    }

    /**
     * @param string|null $linkOnlineSchool
     */
    public function setLinkOnlineSchool(?string $linkOnlineSchool): void
    {
        $this->linkOnlineSchool = $linkOnlineSchool;
    }

    /**
     * @return string|null
     */
    public function getLinkPaymentPage(): ?string
    {
        return $this->linkPaymentPage;
    }

    /**
     * @param string|null $linkPaymentPage
     */
    public function setLinkPaymentPage(?string $linkPaymentPage): void
    {
        $this->linkPaymentPage = $linkPaymentPage;
    }

    /**
     * @return int|null
     */
    public function getTeachableId(): ?int
    {
        return $this->teachableId;
    }

    /**
     * @param int|null $teachableId
     */
    public function setTeachableId(?int $teachableId): void
    {
        $this->teachableId = $teachableId;
    }
}
