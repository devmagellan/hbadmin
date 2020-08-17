<?php

namespace HB\AdminBundle\Entity\SaleFunnel;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Entity\SaleFunnel\Webinar\WebinarBonus;
use HB\AdminBundle\Entity\SaleFunnel\Webinar\WebinarDate;
use HB\AdminBundle\Entity\SaleFunnel\Webinar\WebinarOffer;
use HB\AdminBundle\Entity\SaleFunnel\Webinar\WebinarPromoCode;
use HB\AdminBundle\Entity\SaleFunnel\Webinar\WebinarThesis;
use HB\AdminBundle\Entity\UploadCareFile;

/**
 * SalesFunnel
 *
 * @ORM\Table(name="sales_funnel_webinar")
 * @ORM\Entity()
 */
class SalesFunnelWebinar implements CourseAccessInterface
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
     * @var Course
     *
     * @ORM\ManyToOne(targetEntity="HB\AdminBundle\Entity\Course", inversedBy="salesFunnelWebinar")
     * @ORM\JoinColumn(name="course_id", referencedColumnName="id")
     */
    private $course;

    /**
     * @var UploadCareFile | null
     *
     * @ORM\ManyToOne(targetEntity="HB\AdminBundle\Entity\UploadCareFile")
     * @ORM\JoinColumn(name="image_banner_file_id", referencedColumnName="id", nullable=true)
     */
    private $imageBanner;

    /**
     * @var UploadCareFile | null
     *
     * @ORM\ManyToOne(targetEntity="HB\AdminBundle\Entity\UploadCareFile")
     * @ORM\JoinColumn(name="video_banner_file_id", referencedColumnName="id", nullable=true)
     */
    private $videoBanner;

    /**
     * @var string | null
     *
     * @ORM\Column(name="title", type="string", nullable=true)
     */
    private $title;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time", type="datetime", nullable=true)
     */
    private $time;

    /**
     * @var string
     *
     * @ORM\Column(name="timezone", type="string")
     */
    private $timezone;

    /**
     * @var string | null
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="HB\AdminBundle\Entity\SaleFunnel\Webinar\WebinarThesis", cascade={"remove"})
     * @ORM\JoinTable(name="sales_funnel_webinar_theses",
     *      joinColumns={@ORM\JoinColumn(name="sales_funnel_webinar_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="thesis_id", referencedColumnName="id", onDelete="CASCADE")}
     *      )
     */
    private $theses;

    /**
     * @var string | null
     *
     * @ORM\Column(name="block2homework_description", type="text", nullable=true)
     */
    private $block2HomeworkDescription;

    /**
     * @var UploadCareFile | null
     *
     * @ORM\ManyToOne(targetEntity="HB\AdminBundle\Entity\UploadCareFile")
     * @ORM\JoinColumn(name="b2_homework_file_id", referencedColumnName="id", nullable=true)
     */
    private $block2HomeworkFile;

    /**
     * @var string | null
     *
     * @ORM\Column(name="block2_work_book_description", type="text", nullable=true)
     */
    private $block2WorkBookDescription;

    /**
     * @var UploadCareFile | null
     *
     * @ORM\ManyToOne(targetEntity="HB\AdminBundle\Entity\UploadCareFile")
     * @ORM\JoinColumn(name="b2_workbook_file_id", referencedColumnName="id", nullable=true)
     */
    private $block2WorkBookFile;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="HB\AdminBundle\Entity\SaleFunnel\Webinar\WebinarBonus", cascade={"remove"})
     * @ORM\JoinTable(name="sales_funnel_webinar_bonuses",
     *      joinColumns={@ORM\JoinColumn(name="sales_funnel_webinar_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="bonus_id", referencedColumnName="id", onDelete="CASCADE")}
     *      )
     */
    private $bonuses;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float")
     */
    private $price = 0;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="HB\AdminBundle\Entity\SaleFunnel\Webinar\WebinarPromoCode", cascade={"remove"})
     * @ORM\JoinTable(name="sales_funnel_webinar_promo_codes",
     *      joinColumns={@ORM\JoinColumn(name="sales_funnel_webinar_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="promo_code_id", referencedColumnName="id", onDelete="CASCADE")}
     *      )
     */
    private $promoCodes;

    /**
     * @var bool
     *
     * @ORM\Column(name="auto_webinar", type="boolean")
     */
    private $autoWebinar;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="HB\AdminBundle\Entity\SaleFunnel\Webinar\WebinarOffer", cascade={"remove"})
     * @ORM\JoinTable(name="sales_funnel_webinar_offer",
     *      joinColumns={@ORM\JoinColumn(name="sales_funnel_webinar_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="offer_id", referencedColumnName="id", onDelete="CASCADE")}
     *      )
     */
    private $offers;

    /**
     * @var string | null
     *
     * @ORM\Column(name="offer_description", type="string", nullable=true)
     */
    private $offerDescription;

    /**
     * @var boolean
     *
     * @ORM\Column(name="webinar_record", type="boolean")
     */
    private $webinarRecord;

    /**
     * @var float
     *
     * @ORM\Column(name="webinar_record_price", type="float")
     */
    private $webinarRecordPrice;

    /**
     * @var int
     *
     * @ORM\Column(name="webinar_record_access_days", type="integer")
     */
    private $webinarRecordAccessDays;

    /**
     * @var string | null
     *
     * @ORM\Column(name="external_link", type="string", nullable=true)
     */
    private $externalLink;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="HB\AdminBundle\Entity\SaleFunnel\Webinar\WebinarDate", mappedBy="webinar", cascade={"persist", "remove"})
     */
    private $dates;

    /**
     * SalesFunnelWebinar constructor.
     *
     * @param Course $course
     */
    public function __construct(Course $course)
    {
        $this->course = $course;
        $this->theses = new ArrayCollection();
        $this->bonuses = new ArrayCollection();
        $this->dates = new ArrayCollection();
        $this->timezone = 'Europe/Kiev';

        $this->price = 0;
        $this->promoCodes = new ArrayCollection();
        $this->autoWebinar = false;

        $this->webinarRecord = false;
        $this->webinarRecordPrice = 0;
        $this->webinarRecordAccessDays = 0;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Course
     */
    public function getCourse(): Course
    {
        return $this->course;
    }


    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string|null $title
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return \DateTime
     */
    public function getTime(): \DateTime
    {
        if (!$this->time) {
            return new \DateTime();
        }
        return $this->time;
    }

    /**
     * @param \DateTime $time
     */
    public function setTime(\DateTime $time): void
    {
        $this->time = $time;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @param WebinarThesis $thesis
     */
    public function addThesis(WebinarThesis $thesis)
    {
        $this->theses[] = $thesis;
    }

    /**
     * @param WebinarThesis $thesis
     *
     * @return bool
     */
    public function removeThesis(WebinarThesis $thesis)
    {
        return $this->theses->removeElement($thesis);
    }

    /**
     * @return ArrayCollection
     */
    public function getTheses()
    {
        return $this->theses;
    }

    /**
     * @return UploadCareFile|null
     */
    public function getImageBanner(): ?UploadCareFile
    {
        return $this->imageBanner;
    }

    /**
     * @param UploadCareFile|null $imageBanner
     */
    public function setImageBanner(?UploadCareFile $imageBanner): void
    {
        $this->imageBanner = $imageBanner;
    }

    /**
     * @return UploadCareFile|null
     */
    public function getVideoBanner(): ?UploadCareFile
    {
        return $this->videoBanner;
    }

    /**
     * @param UploadCareFile|null $videoBanner
     */
    public function setVideoBanner(?UploadCareFile $videoBanner): void
    {
        $this->videoBanner = $videoBanner;
    }

    /**
     * @return string|null
     */
    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    /**
     * @param string|null $timezone
     */
    public function setTimezone(?string $timezone): void
    {
        $this->timezone = $timezone;
    }

    /**
     * @return string|null
     */
    public function getBlock2HomeworkDescription(): ?string
    {
        return $this->block2HomeworkDescription;
    }

    /**
     * @param string|null $block2HomeworkDescription
     */
    public function setBlock2HomeworkDescription(?string $block2HomeworkDescription): void
    {
        $this->block2HomeworkDescription = $block2HomeworkDescription;
    }

    /**
     * @return UploadCareFile|null
     */
    public function getBlock2HomeworkFile(): ?UploadCareFile
    {
        return $this->block2HomeworkFile;
    }

    /**
     * @param UploadCareFile|null $block2HomeworkFile
     */
    public function setBlock2HomeworkFile(?UploadCareFile $block2HomeworkFile): void
    {
        $this->block2HomeworkFile = $block2HomeworkFile;
    }

    /**
     * @return string|null
     */
    public function getBlock2WorkBookDescription(): ?string
    {
        return $this->block2WorkBookDescription;
    }

    /**
     * @param string|null $block2WorkBookDescription
     */
    public function setBlock2WorkBookDescription(?string $block2WorkBookDescription): void
    {
        $this->block2WorkBookDescription = $block2WorkBookDescription;
    }

    /**
     * @return UploadCareFile|null
     */
    public function getBlock2WorkBookFile(): ?UploadCareFile
    {
        return $this->block2WorkBookFile;
    }

    /**
     * @param UploadCareFile|null $block2WorkBookFile
     */
    public function setBlock2WorkBookFile(?UploadCareFile $block2WorkBookFile): void
    {
        $this->block2WorkBookFile = $block2WorkBookFile;
    }

    /**
     * @param WebinarBonus $bonus
     */
    public function addBonus(WebinarBonus $bonus)
    {
        $this->bonuses[] = $bonus;
    }

    /**
     * @param WebinarBonus $bonus
     */
    public function removeBonus(WebinarBonus $bonus)
    {
        $this->bonuses->removeElement($bonus);
    }

    /**
     * @return ArrayCollection
     */
    public function getBonuses()
    {
        return $this->bonuses;
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
     * @return ArrayCollection
     */
    public function getPromoCodes()
    {
        return $this->promoCodes;
    }

    /**
     * @param WebinarPromoCode $code
     */
    public function addPromoCode(WebinarPromoCode $code)
    {
        $this->promoCodes[] = $code;
    }

    /**
     * @param WebinarPromoCode $code
     */
    public function removePromoCode(WebinarPromoCode $code)
    {
        $this->promoCodes->removeElement($code);
    }

    /**
     * @return bool
     */
    public function isAutoWebinar(): bool
    {
        return $this->autoWebinar;
    }

    /**
     * @param bool $autoWebinar
     */
    public function setAutoWebinar(bool $autoWebinar): void
    {
        $this->autoWebinar = $autoWebinar;
    }

    /**
     * @param WebinarOffer $offer
     */
    public function addOffer(WebinarOffer $offer)
    {
        $this->offers[] = $offer;
    }

    /**
     * @param WebinarOffer $offer
     */
    public function removeOffer(WebinarOffer $offer)
    {
        $this->offers->removeElement($offer);
    }

    /**
     * @return ArrayCollection
     */
    public function getOffers()
    {
        return $this->offers;
    }

    /**
     * @return bool
     */
    public function isWebinarRecord(): bool
    {
        return $this->webinarRecord;
    }

    /**
     * @param bool $webinarRecord
     */
    public function setWebinarRecord(bool $webinarRecord): void
    {
        $this->webinarRecord = $webinarRecord;
    }

    /**
     * @return float
     */
    public function getWebinarRecordPrice(): float
    {
        return $this->webinarRecordPrice;
    }

    /**
     * @param float $webinarRecordPrice
     */
    public function setWebinarRecordPrice(float $webinarRecordPrice): void
    {
        $this->webinarRecordPrice = $webinarRecordPrice;
    }

    /**
     * @return int
     */
    public function getWebinarRecordAccessDays(): int
    {
        return $this->webinarRecordAccessDays;
    }

    /**
     * @param int $webinarRecordAccessDays
     */
    public function setWebinarRecordAccessDays(int $webinarRecordAccessDays): void
    {
        $this->webinarRecordAccessDays = $webinarRecordAccessDays;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        if ($this->title) {
            return $this->title;
        }

        return '[Без названия]';
    }

    /**
     * @return string|null
     */
    public function getExternalLink(): ?string
    {
        return $this->externalLink;
    }

    /**
     * @param string|null $externalLink
     */
    public function setExternalLink(?string $externalLink): void
    {
        $this->externalLink = $externalLink;
    }

    /**
     * @return ArrayCollection
     */
    public function getDates()
    {
        return $this->dates;
    }

    /**
     * @param ArrayCollection $collection
     */
    public function setDates(ArrayCollection $collection)
    {
        $this->dates = $collection;
    }

    /**
     * @param WebinarDate $date
     */
    public function addDate(WebinarDate $date)
    {
        $this->dates->add($date);
    }

    /**
     * @param WebinarDate $date
     */
    public function removeDate(WebinarDate $date)
    {
        $this->dates->removeElement($date);
    }

    /**
     * @return string|null
     */
    public function getOfferDescription(): ?string
    {
        return $this->offerDescription;
    }

    /**
     * @param string|null $offerDescription
     */
    public function setOfferDescription(?string $offerDescription): void
    {
        $this->offerDescription = $offerDescription;
    }
}
