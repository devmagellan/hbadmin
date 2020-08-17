<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Entity\SaleFunnel\Organic;

use Doctrine\ORM\Mapping as ORM;
use HB\AdminBundle\Entity\UploadCareFile;

/**
 * Feedback video
 * This class used with organic sale funnel
 *
 * @ORM\Table(name="feedback_video")
 * @ORM\Entity()
 */
class FeedBackVideo
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
     * @ORM\Column(name="feed_back_author", type="string")
     */
    private $feedBackAuthor;

    /**
     * @var UploadCareFile
     *
     * @ORM\ManyToOne(targetEntity="HB\AdminBundle\Entity\UploadCareFile")
     * @ORM\JoinColumn(name="feedback_video_file_id", referencedColumnName="id", nullable=true)
     */
    private $feedBackVideo;

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
    public function getFeedBackAuthor(): ?string
    {
        return $this->feedBackAuthor;
    }

    /**
     * @param string $feedBackAuthor
     */
    public function setFeedBackAuthor(string $feedBackAuthor): void
    {
        $this->feedBackAuthor = $feedBackAuthor;
    }

    /**
     * @return UploadCareFile
     */
    public function getFeedBackVideo(): ?UploadCareFile
    {
        return $this->feedBackVideo;
    }

    /**
     * @param UploadCareFile $feedBackVideo
     */
    public function setFeedBackVideo(UploadCareFile $feedBackVideo = null): void
    {
        $this->feedBackVideo = $feedBackVideo;
    }
}