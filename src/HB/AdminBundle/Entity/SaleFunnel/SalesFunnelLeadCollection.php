<?php

namespace HB\AdminBundle\Entity\SaleFunnel;

use Doctrine\ORM\Mapping as ORM;
use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Entity\UploadCareFile;

/**
 * SalesFunnel
 *
 * @ORM\Table(name="sales_funnel_lead_collection")
 * @ORM\Entity()
 */
class SalesFunnelLeadCollection implements CourseAccessInterface
{
    public const LEAD_MAGNET_ORGANIC = 'ORGANIC';
    public const LEAD_MAGNET_WEBINAR = 'WEBINAR';
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
     * @ORM\ManyToOne(targetEntity="HB\AdminBundle\Entity\Course", inversedBy="saleFunnelLeadCollections")
     * @ORM\JoinColumn(name="id_course", referencedColumnName="id", onDelete="CASCADE")
     */
    private $course;

    /**
     * @var string | null
     *
     * @ORM\Column(name="title", type="string", nullable=true)
     */
    private $title;

    /**
     * @var string | null
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string | null
     *
     * @ORM\Column(name="full_description", type="text", nullable=true)
     */
    private $fullDescription;

    /**
     * @var string | null
     *
     * @ORM\Column(name="button_text", type="string", nullable=true)
     */
    private $buttonText;

    /**
     * @var UploadCareFile | null
     *
     * @ORM\ManyToOne(targetEntity="HB\AdminBundle\Entity\UploadCareFile")
     * @ORM\JoinColumn(name="image_file_id", referencedColumnName="id", nullable=true)
     */
    private $image;

    /**
     * @var UploadCareFile | null
     *
     * @ORM\ManyToOne(targetEntity="HB\AdminBundle\Entity\UploadCareFile")
     * @ORM\JoinColumn(name="other_file_id", referencedColumnName="id", nullable=true)
     */
    private $file;

    /**
     * @var bool
     *
     * @ORM\Column(name="lead_magnet_organic", type="boolean")
     */
    private $leadMagnetOrganic;

    /**
     * @var bool
     *
     * @ORM\Column(name="lead_magnet_webinar", type="boolean")
     */
    private $leadMagnetWebinar;

    /**
     * @var bool
     *
     * @ORM\Column(name="lead_magnet_education", type="boolean")
     */
    private $leadMagnetEducation;

    /**
     * @var bool
     *
     * @ORM\Column(name="lead_magnet_down_sell", type="boolean")
     */
    private $leadMagnetDownSell;

    /**
     * @var bool
     *
     * @ORM\Column(name="lead_magnet_layer_cake", type="boolean")
     */
    private $leadMagnetLayerCake;


    /**
     * Constructor
     *
     * @param Course $course
     */
    public function __construct(Course $course)
    {
        $this->course = $course;

        $this->leadMagnetDownSell = false;
        $this->leadMagnetEducation = false;
        $this->leadMagnetLayerCake = false;
        $this->leadMagnetOrganic = false;
        $this->leadMagnetWebinar = false;
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
     * @param Course $course
     */
    public function setCourse(Course $course): void
    {
        $this->course = $course;
    }

    /**
     * @return null|string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param null|string $title
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return null|string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param null|string $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return UploadCareFile
     */
    public function getImage(): ?UploadCareFile
    {
        return $this->image;
    }

    /**
     * @param UploadCareFile | null $image
     */
    public function setImage(UploadCareFile $image = null): void
    {
        $this->image = $image;
    }

    /**
     * @return UploadCareFile
     */
    public function getFile(): ?UploadCareFile
    {
        return $this->file;
    }

    /**
     * @param UploadCareFile | null $file
     */
    public function setFile(UploadCareFile $file = null): void
    {
        $this->file = $file;
    }

    /**
     * @return bool
     */
    public function isLeadMagnetOrganic(): bool
    {
        return $this->leadMagnetOrganic;
    }

    /**
     * @param bool $leadMagnetOrganic
     */
    public function setLeadMagnetOrganic(bool $leadMagnetOrganic): void
    {
        $this->leadMagnetOrganic = $leadMagnetOrganic;
    }

    /**
     * @return bool
     */
    public function isLeadMagnetWebinar(): bool
    {
        return $this->leadMagnetWebinar;
    }

    /**
     * @param bool $leadMagnetWebinar
     */
    public function setLeadMagnetWebinar(bool $leadMagnetWebinar): void
    {
        $this->leadMagnetWebinar = $leadMagnetWebinar;
    }

    /**
     * @return bool
     */
    public function isLeadMagnetEducation(): bool
    {
        return $this->leadMagnetEducation;
    }

    /**
     * @param bool $leadMagnetEducation
     */
    public function setLeadMagnetEducation(bool $leadMagnetEducation): void
    {
        $this->leadMagnetEducation = $leadMagnetEducation;
    }

    /**
     * @return bool
     */
    public function isLeadMagnetDownSell(): bool
    {
        return $this->leadMagnetDownSell;
    }

    /**
     * @param bool $leadMagnetDownSell
     */
    public function setLeadMagnetDownSell(bool $leadMagnetDownSell): void
    {
        $this->leadMagnetDownSell = $leadMagnetDownSell;
    }

    /**
     * @return bool
     */
    public function isLeadMagnetLayerCake(): bool
    {
        return $this->leadMagnetLayerCake;
    }

    /**
     * @param bool $leadMagnetLayerCake
     */
    public function setLeadMagnetLayerCake(bool $leadMagnetLayerCake): void
    {
        $this->leadMagnetLayerCake = $leadMagnetLayerCake;
    }

    /**
     * @return string|null
     */
    public function getButtonText (): ?string
    {
        return $this->buttonText;
    }

    /**
     * @param string|null $buttonText
     */
    public function setButtonText (?string $buttonText): void
    {
        $this->buttonText = $buttonText;
    }

    /**
     * @return string|null
     */
    public function getFullDescription(): ?string
    {
        return $this->fullDescription;
    }

    /**
     * @param string|null $fullDescription
     */
    public function setFullDescription(?string $fullDescription): void
    {
        $this->fullDescription = $fullDescription;
    }

    /**
     * @return array
     */
    public function getLeadMagnets(): array
    {
        $leadMagnets = [];

        if ($this->leadMagnetLayerCake) {
            $leadMagnets[] = 'Слоеный пирог';
        }

        if ($this->leadMagnetDownSell) {
            $leadMagnets[] = 'Down Sell';
        }

        if ($this->leadMagnetOrganic) {
            $leadMagnets[] = 'Целевая страница';
        }

        if ($this->leadMagnetWebinar) {
            $leadMagnets[] = 'Вебинар';
        }

        if ($this->leadMagnetEducation) {
            $leadMagnets[] = 'Образовательная';
        }

        return $leadMagnets;
    }
}
