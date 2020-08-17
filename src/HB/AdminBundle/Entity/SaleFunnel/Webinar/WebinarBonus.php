<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Entity\SaleFunnel\Webinar;

use Doctrine\ORM\Mapping as ORM;
use HB\AdminBundle\Entity\UploadCareFile;

/**
 * Webinar thesis
 * This class used with webinar sale funnel
 *
 * @ORM\Table(name="webinar_bonus")
 * @ORM\Entity()
 */
class WebinarBonus
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
     * @ORM\Column(name="title", type="text", nullable=false)
     */
    private $title;

    /**
     * @var UploadCareFile | null
     *
     * @ORM\ManyToOne(targetEntity="HB\AdminBundle\Entity\UploadCareFile")
     * @ORM\JoinColumn(name="bonus_file_id", referencedColumnName="id", nullable=true)
     */
    private $file;

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
    public function getFile(): ?UploadCareFile
    {
        return $this->file;
    }

    /**
     * @param UploadCareFile|null $file
     */
    public function setFile(?UploadCareFile $file): void
    {
        $this->file = $file;
    }

}