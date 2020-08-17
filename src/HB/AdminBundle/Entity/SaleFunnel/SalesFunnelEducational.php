<?php

namespace HB\AdminBundle\Entity\SaleFunnel;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Entity\SaleFunnel\Educational\Letter;
use HB\AdminBundle\Entity\UploadCareFile;

/**
 * SalesFunnel
 *
 * @ORM\Table(name="sales_funnel_educational")
 * @ORM\Entity()
 *
 */
class SalesFunnelEducational implements CourseAccessInterface
{
    public const LETTERS_FILE = 'LESSONS_FILE';
    public const ARTICLES_FILE = 'ARTICLES_FILE';

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
     * @ORM\OneToOne(targetEntity="HB\AdminBundle\Entity\Course", inversedBy="salesFunnelEducational")
     * @ORM\JoinColumn(name="id_course", referencedColumnName="id")
     */
    private $course;

    /**
     * @ORM\ManyToMany(targetEntity="HB\AdminBundle\Entity\SaleFunnel\Educational\Letter", cascade={"remove"})
     * @ORM\JoinTable(name="sales_funnel_educational_letters",
     *      joinColumns={@ORM\JoinColumn(name="sales_funnel_educational_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="letter_id", referencedColumnName="id", onDelete="CASCADE")}
     *      )
     */
    private $letters;

    /**
     * @todo rename to letters_file_id
     * @var UploadCareFile | null
     *
     * @ORM\ManyToOne(targetEntity="HB\AdminBundle\Entity\UploadCareFile")
     * @ORM\JoinColumn(name="lessons_file_id", referencedColumnName="id", nullable=true)
     */
    private $lettersFile;

    /**
     * @var UploadCareFile | null
     *
     * @ORM\ManyToOne(targetEntity="HB\AdminBundle\Entity\UploadCareFile")
     * @ORM\JoinColumn(name="articles_file_id", referencedColumnName="id", nullable=true)
     */
    private $articlesFile;

    /**
     * @var string | null
     *
     * @ORM\Column(name="external_link", type="string", nullable=true)
     */
    private $externalLink;

    /**
     * Constructor
     *
     * @param Course $course
     */
    public function __construct(Course $course)
    {
        $this->course = $course;
        $this->letters = new ArrayCollection();
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
     * @param Letter $letter
     */
    public function addLetter(Letter $letter)
    {
        $this->letters[] = $letter;
    }

    /**
     * @param Letter $letter
     */
    public function removeLetter(Letter $letter)
    {
        $this->letters->removeElement($letter);
    }

    /**
     * @return ArrayCollection
     */
    public function getLetters()
    {
        return $this->letters;
    }

    /**
     * @return UploadCareFile|null
     */
    public function getLettersFile(): ?UploadCareFile
    {
        return $this->lettersFile;
    }

    /**
     * @param UploadCareFile|null $lettersFile
     */
    public function setLettersFile(?UploadCareFile $lettersFile): void
    {
        $this->lettersFile = $lettersFile;
    }

    /**
     * @return UploadCareFile|null
     */
    public function getArticlesFile(): ?UploadCareFile
    {
        return $this->articlesFile;
    }

    /**
     * @param UploadCareFile|null $articlesFile
     */
    public function setArticlesFile(?UploadCareFile $articlesFile): void
    {
        $this->articlesFile = $articlesFile;
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
}
