<?php

namespace HB\AdminBundle\Entity\SaleFunnel;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Entity\UploadCareFile;
use HB\AdminBundle\Validator\SaleFunnelOneTimeOfferValidator;

/**
 * SalesFunnel
 *
 * @ORM\Table(name="sales_funnel_one_time_offer")
 * @ORM\Entity()
 */
class SalesFunnelOneTimeOffer implements CourseAccessInterface
{
    public const ONE_STEP_LEVEL = 'ONE';
    public const TWO_STEP_LEVEL = 'TWO';

    public const OTO_FILE_TYPE_OFFER = 'OFFER';
    public const OTO_FILE_TYPE_IMAGE = 'IMAGE';
    public const OTO_FILE_TYPE_VIDEO = 'VIDEO';

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
     * @ORM\ManyToOne(targetEntity="HB\AdminBundle\Entity\Course", inversedBy="saleFunnelOneTimeOffer")
     * @ORM\JoinColumn(name="id_course", referencedColumnName="id")
     */
    private $course;

    /**
     * @var string
     *
     * @ORM\Column(name="level", type="string", nullable=false)
     */
    private $level = self::ONE_STEP_LEVEL;

    /**
     * @var string | null
     *
     * @ORM\Column(name="thank_text", type="text", nullable=true)
     */
    private $thankText = 'Спасибо за [действие], доступ к [контент] уже у вас на почте! Проверьте почту сейчас!';

    /**
     * @var string | null
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description = 'Однако любая теория, ничего не стоит без практики. Поэтому в качестве поддержки ваших навыков, я хочу сделать вам уникальное предложение.';

    /**
     * @var string | null
     *
     * @ORM\Column(name="video_text", type="text", nullable=true)
     */
    private $videoText = 'Текстовое поле, в котором по умолчанию стоит следующий текст: Обязательно посмотрите это видео о том, чем полезен [название продукта ОТО]';

    /**
     * @var UploadCareFile | null
     *
     * @ORM\ManyToOne(targetEntity="HB\AdminBundle\Entity\UploadCareFile")
     * @ORM\JoinColumn(name="video_file", referencedColumnName="id", nullable=true)
     */
    private $video;

    /**
     * @var UploadCareFile | null
     *
     * @ORM\ManyToOne(targetEntity="HB\AdminBundle\Entity\UploadCareFile")
     * @ORM\JoinColumn(name="image_file", referencedColumnName="id", nullable=true)
     */
    private $image;

    /**
     * @var UploadCareFile | null
     *
     * @ORM\ManyToOne(targetEntity="HB\AdminBundle\Entity\UploadCareFile")
     * @ORM\JoinColumn(name="offer_file", referencedColumnName="id", nullable=true)
     */
    private $offerFile;

    /**
     * @var string | null
     *
     * @ORM\Column(name="additional_text", type="text", nullable=true)
     */
    private $additionalText;

    /**
     * @var string | null
     *
     * @ORM\Column(name="link", type="text", nullable=true)
     */
    private $link;

    /**
     * @var bool
     *
     * @ORM\Column(name="for_webinar", type="boolean")
     */
    private $forWebinar;

    /**
     * @var bool
     *
     * @ORM\Column(name="for_lead_magnet", type="boolean")
     */
    private $forLeadMagnet;

    /**
     * @var bool
     *
     * @ORM\Column(name="for_educational", type="boolean")
     */
    private $forEducational;

    /**
     * @var bool
     *
     * @ORM\Column(name="for_mini_course", type="boolean")
     */
    private $forMiniCourse;

   /**
     * @var string | null
     *
     * @ORM\Column(name="external_link", type="string", nullable=true)
     */
    private $externalLink;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="HB\AdminBundle\Entity\SaleFunnel\OneTimeOffer", mappedBy="funnel", cascade={"all"})
     */
    private $offers;

    /**
     * Constructor
     *
     * @param Course $course
     */
    public function __construct(Course $course)
    {
        $this->course = $course;

        $this->offers = new ArrayCollection();

        $this->forEducational = false;
        $this->forLeadMagnet = false;
        $this->forMiniCourse = false;
        $this->forWebinar = false;
    }

    /**
     * @return int
     */
    public function getId(): ?int
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
     * @return string
     */
    public function getLevel(): string
    {
        return $this->level;
    }

    /**
     * @param string $level
     */
    public function setLevel(string $level): void
    {
        $this->level = $level;
    }



    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }


    /**
     * @return bool
     */
    public function isForWebinar(): bool
    {
        return $this->forWebinar;
    }

    /**
     * @param bool $forWebinar
     */
    public function setForWebinar(bool $forWebinar): void
    {
        $this->forWebinar = $forWebinar;
    }

    /**
     * @return bool
     */
    public function isForLeadMagnet(): bool
    {
        return $this->forLeadMagnet;
    }

    /**
     * @param bool $forLeadMagnet
     */
    public function setForLeadMagnet(bool $forLeadMagnet): void
    {
        $this->forLeadMagnet = $forLeadMagnet;
    }

    /**
     * @return bool
     */
    public function isForEducational(): bool
    {
        return $this->forEducational;
    }

    /**
     * @param bool $forEducational
     */
    public function setForEducational(bool $forEducational): void
    {
        $this->forEducational = $forEducational;
    }

    /**
     * @return bool
     */
    public function isForMiniCourse(): bool
    {
        return $this->forMiniCourse;
    }

    /**
     * @param bool $forMiniCourse
     */
    public function setForMiniCourse(bool $forMiniCourse): void
    {
        $this->forMiniCourse = $forMiniCourse;
    }


    /**
     * @return array
     */
    public function getPurposes(): array
    {
        $purposes = [];

        if ($this->forMiniCourse) {
            $purposes[] = 'Мини курс';
        }
        if ($this->forLeadMagnet) {
            $purposes[] = 'Сбор лидов';
        }
        if ($this->forEducational) {
            $purposes[] = 'Образовательная';
        }
        if ($this->forWebinar) {
            $purposes[] = 'Вебинар';
        }

        return $purposes;
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
     * @return string|null
     */
    public function getThankText(): ?string
    {
        return $this->thankText;
    }

    /**
     * @param string|null $thankText
     */
    public function setThankText(?string $thankText): void
    {
        $this->thankText = $thankText;
    }

    /**
     * @return UploadCareFile|null
     */
    public function getVideo(): ?UploadCareFile
    {
        return $this->video;
    }

    /**
     * @param UploadCareFile|null $video
     */
    public function setVideo(?UploadCareFile $video): void
    {
        $this->video = $video;
    }

    /**
     * @return UploadCareFile|null
     */
    public function getImage(): ?UploadCareFile
    {
        return $this->image;
    }

    /**
     * @param UploadCareFile|null $image
     */
    public function setImage(?UploadCareFile $image): void
    {
        $this->image = $image;
    }

    /**
     * @return UploadCareFile|null
     */
    public function getOfferFile(): ?UploadCareFile
    {
        return $this->offerFile;
    }

    /**
     * @param UploadCareFile|null $offerFile
     */
    public function setOfferFile(?UploadCareFile $offerFile): void
    {
        $this->offerFile = $offerFile;
    }

    /**
     * @return string|null
     */
    public function getAdditionalText(): ?string
    {
        return $this->additionalText;
    }

    /**
     * @param string|null $additionalText
     */
    public function setAdditionalText(?string $additionalText): void
    {
        $this->additionalText = $additionalText;
    }

    /**
     * @return string|null
     */
    public function getLink(): ?string
    {
        return $this->link;
    }

    /**
     * @param string|null $link
     */
    public function setLink(?string $link): void
    {
        $this->link = $link;
    }

    /**
     * @param OneTimeOffer $offer
     */
    public function addOffer(OneTimeOffer $offer)
    {
        $this->offers[] = $offer;
    }

    /**
     * @param OneTimeOffer $offer
     */
    public function removeOffer(OneTimeOffer $offer)
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
     * @return string|null
     */
    public function getVideoText(): ?string
    {
        return $this->videoText;
    }

    /**
     * @param string|null $videoText
     */
    public function setVideoText(?string $videoText): void
    {
        $this->videoText = $videoText;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return !(bool) SaleFunnelOneTimeOfferValidator::validate($this);
    }
}
