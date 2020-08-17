<?php

declare(strict_types = 1);

namespace HB\AdminBundle\Entity\Teachable;

use Doctrine\ORM\Mapping as ORM;

/**
 * WebHook
 *
 * @ORM\Table(name="teachable_lecture_progress")
 * @ORM\Entity()
 */
class TeachableLectureProgress implements TeachableCourseStudentInterface
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
     * @var null|int
     *
     * @ORM\Column(name="lecture_id", type="integer", nullable=true)
     */
    private $lecture_id;

    /**
     * @var string
     *
     * @ORM\Column(name="course_name", type="string", nullable=true)
     */
    private $courseName;

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
     * @var \DateTime
     *
     * @ORM\Column(name="last_signed_at", type="datetime", nullable=false)
     */
    private $lastSignedAt;

    /**
     * @var null|int
     *
     * @ORM\Column(name="percent_complete", type="integer", nullable=true)
     */
    private $percentComplete;

    /**
     * @var int
     *
     * @ORM\Column(name="comments_rating", type="integer", nullable=false)
     */
    private $commentsRating = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="correct_answers", type="integer", nullable=false)
     */
    private $correctAnswers = 0;

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
     * Update existed Lecture progress
     * @param array $data
     */
    private function update(array $data)
    {
        foreach ($data as $property => $value) {
            if (property_exists($this, $property)) {
                $this->$property = $value;
            }
        }
    }

    /**
     * @param array                           $data
     * @param TeachableLectureProgress | null $current
     *
     * @return TeachableLectureProgress
     * @throws \Exception
     */
    public static function fromWebhook(array $data, TeachableLectureProgress $current = null): TeachableLectureProgress
    {
        $preparedProperties = [];

        $preparedProperties['createdAt'] = new \DateTime($data['created']);
        $preparedProperties['hookEventId'] = $data['hook_event_id'];

        $preparedProperties['course_id'] = (int) $data['object']['course_id'];
        $preparedProperties['lecture_id'] = $data['object']['lecture_id'];
        $preparedProperties['courseName'] = $data['object']['course']['name'];
        $preparedProperties['percentComplete'] = $data['object']['percent_complete'];

        $preparedProperties['studentName'] = $data['object']['user']['name'];
        $preparedProperties['studentEmail'] = $data['object']['user']['email'];
        $preparedProperties['lastSignedAt'] = new \DateTime($data['object']['user']['last_sign_in_at']);

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
        return $this->course_id;
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
     * @return \DateTime
     */
    public function getLastSignedAt(): \DateTime
    {
        return $this->lastSignedAt;
    }

    /**
     * @return string | null
     */
    public function getCourseName(): ?string
    {
        return $this->courseName;
    }

    /**
     * @return int|null
     */
    public function getLectureId(): ?int
    {
        return $this->lecture_id;
    }

    /**
     * @return int|null
     */
    public function getPercentComplete(): ?int
    {
        return $this->percentComplete;
    }

    /**
     * @return int
     */
    public function getCommentsRating(): int
    {
        return $this->commentsRating;
    }

    /**
     * @return int
     */
    public function getCorrectAnswers(): int
    {
        return $this->correctAnswers;
    }

    /**
     * @param int $commentsRating
     * @param int $correctAnswers
     */
    public function updateStatistic(int $commentsRating, int $correctAnswers)
    {
        $this->correctAnswers = $correctAnswers;
        $this->commentsRating = $commentsRating;
    }
}
