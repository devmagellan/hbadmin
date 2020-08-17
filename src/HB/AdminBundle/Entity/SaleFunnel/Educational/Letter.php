<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Entity\SaleFunnel\Educational;

use Doctrine\ORM\Mapping as ORM;
use HB\AdminBundle\Entity\UploadCareFile;

/**
 * Educational letter
 * This class used with educational sale funnel
 *
 * @ORM\Table(name="educational_letter")
 * @ORM\Entity()
 */
class Letter
{
    public const LESSON_FILE = 'LESSON_FILE';
    public const ARTICLE_FILE = 'ARTICLE_FILE';

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
     * @ORM\Column(name="name", type="string", nullable=false)
     */
    private $name;

    /**
     * @var UploadCareFile
     *
     * @ORM\ManyToOne(targetEntity="HB\AdminBundle\Entity\UploadCareFile")
     * @ORM\JoinColumn(name="lesson_file_id", referencedColumnName="id", nullable=true)
     */
    private $lessonFile;

    /**
     * @var UploadCareFile
     *
     * @ORM\ManyToOne(targetEntity="HB\AdminBundle\Entity\UploadCareFile")
     * @ORM\JoinColumn(name="article_file_id", referencedColumnName="id", nullable=true)
     */
    private $articleFile;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return UploadCareFile
     */
    public function getLessonFile(): ?UploadCareFile
    {
        return $this->lessonFile;
    }

    /**
     * @param UploadCareFile | null $lessonFile
     */
    public function setLessonFile(?UploadCareFile $lessonFile): void
    {
        $this->lessonFile = $lessonFile;
    }

    /**
     * @return UploadCareFile
     */
    public function getArticleFile(): ?UploadCareFile
    {
        return $this->articleFile;
    }

    /**
     * @param UploadCareFile | null $articleFile
     */
    public function setArticleFile(?UploadCareFile $articleFile): void
    {
        $this->articleFile = $articleFile;
    }
}