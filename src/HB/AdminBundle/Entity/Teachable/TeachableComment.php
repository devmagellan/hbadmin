<?php

declare(strict_types = 1);

namespace HB\AdminBundle\Entity\Teachable;

use Doctrine\ORM\Mapping as ORM;
use HB\AdminBundle\Helper\DateIntervalHelper;

/**
 * WebHook
 *
 * @ORM\Table(name="teachable_comment")
 * @ORM\Entity()
 */
class TeachableComment
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
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var int
     *
     * @ORM\Column(name="hook_event_id", type="integer", nullable=false)
     */
    private $hookEventId;

    /**
     * @var int
     *
     * @ORM\Column(name="course_id", type="integer", nullable=false)
     */
    private $course_id;

    /**
     * @var int | null
     *
     * @ORM\Column(name="lecture_id", type="integer", nullable=true)
     */
    private $lecture_id;

    /**
     * @var string
     *
     * @ORM\Column(name="student_name", type="string", nullable=false)
     */
    private $studentName;

    /**
     * @var string
     *
     * @ORM\Column(name="student_email", type="string", nullable=false)
     */
    private $studentEmail;

    /**
     * @var int
     *
     * @ORM\Column(name="comment_id", type="bigint", nullable=false)
     */
    private $commentId;

    /**
     * @var string
     *
     * @ORM\Column(name="full_url", type="string", nullable=false)
     */
    private $fullUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="comment_type", type="string", nullable=false)
     */
    private $type;

    /**
     * @var int | null
     *
     * @ORM\Column(name="rating", type="integer", nullable=true)
     */
    private $rating;

    /**
     * TeachableTransaction constructor.
     *
     * @param array $data
     */
    private function __construct(array $data)
    {
        foreach ($data as $property => $value) {
            if (property_exists($this, $property)) {
                $this->$property = $value;
            }
        }
    }

    /**
     * Update existed comment
     *
     * @param array $data
     */
    public function update(array $data)
    {
        foreach ($data as $property => $value) {
            if (property_exists($this, $property)) {
                $this->$property = $value;
            }
        }
    }

    /**
     * @param array                   $data
     * @param TeachableComment | null $current
     *
     * @return TeachableComment
     * @throws \Exception
     */
    public static function fromWebhook(array $data, TeachableComment $current = null): TeachableComment
    {
        $preparedProperties = [];

        $preparedProperties['createdAt'] = new \DateTime($data['created']);
        $preparedProperties['hookEventId'] = $data['hook_event_id'];

        $preparedProperties['studentName'] = $data['object']['user']['name'];
        $preparedProperties['studentEmail'] = $data['object']['user']['email'];

        $preparedProperties['commentId'] = $data['object']['id'];

        $preparedProperties['type'] = $data['object']['commentable']['attachable_type'];
        $preparedProperties['fullUrl'] = $data['object']['commentable']['full_url'];

        self::parseUrl($preparedProperties['fullUrl'], $preparedProperties);

        if ($current) {
            $current->update($preparedProperties);

            return $current;
        }

        return new self($preparedProperties);
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return int
     */
    public function getHookEventId(): int
    {
        return $this->hookEventId;
    }

    /**
     * @return int
     */
    public function getCourseId(): int
    {
        return (int) $this->course_id;
    }

    /**
     * @return string
     */
    public function getStudentName(): string
    {
        return $this->studentName;
    }

    /**
     * @return string
     */
    public function getStudentEmail(): string
    {
        return $this->studentEmail;
    }

    /**
     * @param string $url
     * @param array  $data
     */
    public static function parseUrl(string $url, array &$data)
    {
        // ex.: https://heartbeat.education/courses/527740/lectures/10955584
        $parts = explode('/', $url);

        $courseKey = array_search('courses', $parts);
        if ($courseKey) {
            $data['course_id'] = $parts[$courseKey + 1];
        }

        $lectureKey = array_search('lectures', $parts);
        if ($lectureKey) {
            $data['lecture_id'] = $parts[$lectureKey + 1];
        }

    }

    /**
     * @param int|null $rating
     */
    public function setRating(?int $rating): void
    {
        $this->rating = $rating;
    }

    /**
     * @return int|null
     */
    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function getDateIntervalText()
    {
        return DateIntervalHelper::getIntervalText($this->createdAt);
    }

    /**
     * @return string
     */
    public function getFullUrl(): ?string
    {
        return $this->fullUrl;
    }

    /**
     * @return int | null
     */
    public function getCommentId(): ?int
    {
        return (int) $this->commentId;
    }

    /**
     * @return string
     */
    public function getReplyUrl()
    {
        if ($this->course_id && $this->lecture_id && $this->commentId) {
            $url = 'https://heartbeat.education/courses/'.$this->course_id.'/lectures/'.$this->lecture_id.'/comments/'.$this->commentId;
        } else {
            $url = $this->fullUrl;
        }

        return $url;
    }

}
