<?php

namespace HB\AdminBundle\Entity\SaleFunnel\MiniCourse;

use Doctrine\ORM\Mapping as ORM;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelHotLeads;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelMiniCourse;
use HB\AdminBundle\Entity\UploadCareFile;

/**
 * @ORM\Table(name="sales_funnel_mini_course_lesson")
 * @ORM\Entity()
 *
 */
class MiniLesson
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
     * @var string
     *
     * @ORM\Column(name="title", type="string", nullable=false)
     */
    private $title;

    /**
     * @var SalesFunnelHotLeads
     *
     * @ORM\ManyToOne(targetEntity="HB\AdminBundle\Entity\SaleFunnel\SalesFunnelMiniCourse", inversedBy="lessons")
     * @ORM\JoinColumn(name="funnel_mini_course_id", referencedColumnName="id")
     */
    private $funnelMiniCourse;

    /**
     * @var UploadCareFile | null
     *
     * @ORM\ManyToOne(targetEntity="HB\AdminBundle\Entity\UploadCareFile")
     * @ORM\JoinColumn(name="lesson_file_id", referencedColumnName="id", nullable=true)
     */
    private $lessonFile;

    /**
     * @var UploadCareFile | null
     *
     * @ORM\ManyToOne(targetEntity="HB\AdminBundle\Entity\UploadCareFile")
     * @ORM\JoinColumn(name="homework_file_id", referencedColumnName="id", nullable=true)
     */
    private $homeWorkFile;

    /**
     * @var string | null
     *
     * @ORM\Column(name="lesson_text", type="text", nullable=true)
     */
    private $lessonText;

    /**
     * @var string | null
     *
     * @ORM\Column(name="homework_text", type="text", nullable=true)
     */
    private $homeworkText;



    /**
     * Constructor
     *
     * @param SalesFunnelMiniCourse $funnelMiniCourse
     */
    public function __construct(SalesFunnelMiniCourse $funnelMiniCourse)
    {
        $this->funnelMiniCourse = $funnelMiniCourse;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return SalesFunnelMiniCourse
     */
    public function getFunnelMiniCourse(): SalesFunnelMiniCourse
    {
        return $this->funnelMiniCourse;
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
     * @return UploadCareFile|null
     */
    public function getLessonFile(): ?UploadCareFile
    {
        return $this->lessonFile;
    }

    /**
     * @param UploadCareFile|null $lessonFile
     */
    public function setLessonFile(?UploadCareFile $lessonFile): void
    {
        $this->lessonFile = $lessonFile;
    }

    /**
     * @return UploadCareFile|null
     */
    public function getHomeWorkFile(): ?UploadCareFile
    {
        return $this->homeWorkFile;
    }

    /**
     * @param UploadCareFile|null $homeWorkFile
     */
    public function setHomeWorkFile(?UploadCareFile $homeWorkFile): void
    {
        $this->homeWorkFile = $homeWorkFile;
    }

    /**
     * @return string|null
     */
    public function getLessonText(): ?string
    {
        return $this->lessonText;
    }

    /**
     * @param string|null $lessonText
     */
    public function setLessonText(?string $lessonText): void
    {
        $this->lessonText = $lessonText;
    }

    /**
     * @return string|null
     */
    public function getHomeworkText(): ?string
    {
        return $this->homeworkText;
    }

    /**
     * @param string|null $homeworkText
     */
    public function setHomeworkText(?string $homeworkText): void
    {
        $this->homeworkText = $homeworkText;
    }

}
