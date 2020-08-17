<?php

declare(strict_types = 1);

namespace HB\AdminBundle\Entity\Teachable;

use Doctrine\ORM\Mapping as ORM;

/**
 * WebHook
 *
 * @ORM\Table(name="teachable_enrollment")
 * @ORM\Entity()
 */
class TeachableEnrollment implements TeachableCourseStudentInterface
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
     * @ORM\Column(name="last_activity_at", type="datetime", nullable=false)
     */
    private $lastActivityAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_signed_at", type="datetime", nullable=true)
     */
    private $lastSignedAt;

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
     * Update existed enrollment
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
     * @param array                      $data
     * @param TeachableEnrollment | null $current
     *
     * @return TeachableEnrollment
     * @throws \Exception
     */
    public static function fromWebhook(array $data, TeachableEnrollment $current = null): TeachableEnrollment
    {
        $preparedProperties = [];

        $preparedProperties['createdAt'] = new \DateTime($data['created']);
        $preparedProperties['hookEventId'] = $data['hook_event_id'];

        $preparedProperties['course_id'] = $data['object']['course']['id'];
        $preparedProperties['courseName'] = $data['object']['course']['name'];

        $preparedProperties['studentName'] = $data['object']['user']['name'];
        $preparedProperties['studentEmail'] = $data['object']['user']['email'];

        if (isset($data['object']['user']['joined_at'])) {
            $preparedProperties['lastActivityAt'] =  new \DateTime($data['object']['user']['joined_at']);
        }

        if (isset($data['object']['user']['current_sign_in_at'])) {
            $preparedProperties['lastSignedAt'] = new \DateTime($data['object']['user']['current_sign_in_at']);
        }



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
     * @return \DateTime | null
     */
    public function getLastActivityAt(): ?\DateTime
    {
        return $this->lastActivityAt;
    }

    /**
     * @return string | null
     */
    public function getCourseName(): ?string
    {
        return $this->courseName;
    }

    /**
     * @return \DateTime | null
     */
    public function getLastSignedAt(): ?\DateTime
    {
        return $this->lastSignedAt;
    }

}
