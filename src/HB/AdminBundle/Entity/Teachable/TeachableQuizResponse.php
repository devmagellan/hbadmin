<?php

declare(strict_types = 1);

namespace HB\AdminBundle\Entity\Teachable;

use Doctrine\ORM\Mapping as ORM;

/**
 * WebHook
 *
 * @ORM\Table(name="teachable_quiz_response")
 * @ORM\Entity()
 */
class TeachableQuizResponse
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
     * @var int | null
     *
     * @ORM\Column(name="course_id", type="integer", nullable=true)
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
     * @ORM\Column(name="student_email", type="string", nullable=false)
     */
    private $studentEmail;

    /**
     * @var int
     *
     * @ORM\Column(name="quiz_id", type="integer", nullable=false)
     */
    private $quizId;

    /**
     * @var int
     *
     * @ORM\Column(name="correct_answers", type="integer", nullable=false)
     */
    private $correctAnswers = 0;

    /**
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
     * @param TeachableQuizResponse | null $current
     *
     * @return TeachableQuizResponse
     * @throws \Exception
     */
    public static function fromWebhook(array $data, TeachableQuizResponse $current = null): TeachableQuizResponse
    {
        $preparedProperties = [];

        $preparedProperties['createdAt'] = new \DateTime($data['created']);
        $preparedProperties['hookEventId'] = $data['hook_event_id'];

        $preparedProperties['lecture_id'] = $data['object']['custom_form']['attachment']['attachable_id'];
        $preparedProperties['quizId'] = $data['object']['custom_form_id'];


        $preparedProperties['correctAnswers'] = $data['object']['grade']['correct'];

        $preparedProperties['studentEmail'] = $data['object']['user']['email'];

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
    public function getCourseId()
    {
        return $this->course_id;
    }

    /**
     * @param int|null $course_id
     */
    public function setCourseId(?int $course_id): void
    {
        $this->course_id = $course_id;
    }

    /**
     * @return string
     */
    public function getStudentEmail(): string
    {
        return $this->studentEmail;
    }

    /**
     * @return int|null
     */
    public function getLectureId(): ?int
    {
        return $this->lecture_id;
    }

    /**
     * @return int
     */
    public function getQuizId(): int
    {
        return $this->quizId;
    }

    /**
     * @return int
     */
    public function getCorrectAnswers(): int
    {
        return $this->correctAnswers;
    }
}
