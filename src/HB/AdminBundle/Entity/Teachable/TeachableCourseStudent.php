<?php

declare(strict_types = 1);

namespace HB\AdminBundle\Entity\Teachable;

use Doctrine\ORM\Mapping as ORM;
use HB\AdminBundle\Helper\DateIntervalHelper;

/**
 * @ORM\Table(name="teachable_course_student", uniqueConstraints={@ORM\UniqueConstraint(name="course_student_unique_idx", columns={"course_id", "student_email"})}  )
 * @ORM\Entity()
 */
class TeachableCourseStudent implements TeachableCourseStudentInterface
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
     * @var \DateTime | null
     *
     * @ORM\Column(name="last_activity_at", type="datetime", nullable=true)
     */
    private $lastActivityAt;

    /**
     * @var \DateTime | null
     *
     * @ORM\Column(name="last_sign_in_at", type="datetime", nullable=true)
     */
    private $lastSignInAt;

    /**
     * @param array $data
     */
    private function __construct(array $data)
    {
        $this->createdAt = new \DateTime();

        foreach ($data as $property => $value) {
            if (property_exists($this, $property)) {
                $this->$property = $value;
            }
        }
    }

    /**
     * @param TeachableCourseStudentInterface $object
     *
     * @return TeachableCourseStudent
     * @throws \Exception
     */
    public static function fromInterface(TeachableCourseStudentInterface $object): TeachableCourseStudent
    {
        $student = new self([]);
        $student->setStudentEmail($object->getStudentEmail());
        $student->setCourseId($object->getCourseId());
        $student->setStudentName($object->getStudentName());

        return $student;
    }

    public function update(TeachableCourseStudentInterface $teachableCourseStudentData)
    {
        $this->setStudentName($teachableCourseStudentData->getStudentName());
        $this->setCourseName($teachableCourseStudentData->getCourseName());
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
     * @param int $course_id
     */
    public function setCourseId(int $course_id): void
    {
        $this->course_id = $course_id;
    }

    /**
     * @param string $studentName
     */
    public function setStudentName(string $studentName): void
    {
        $this->studentName = $studentName;
    }

    /**
     * @param string $studentEmail
     */
    public function setStudentEmail(string $studentEmail): void
    {
        $this->studentEmail = $studentEmail;
    }

    /**
     * @return \DateTime | null
     */
    public function getLastActivityAt(): ?\DateTime
    {
        return $this->lastActivityAt;
    }

    /**
     * @return string
     */
    public function lastActivityText(): string
    {
        $str = '-';
        if ($this->lastActivityAt) {
            $str = DateIntervalHelper::getIntervalText($this->lastActivityAt);
        }
        return $str;
    }

    /**
     * @return \DateTime|null
     */
    public function getLastSignInAt(): ?\DateTime
    {
        return $this->lastSignInAt;
    }

    /**
     * @return string
     */
    public function lastSignInAtText(): string
    {
        $str = '-';

        if ($this->lastSignInAt) {
            $str = DateIntervalHelper::getIntervalText($this->lastSignInAt);
        }

        return $str;
    }

    /**
     * @param TeachableEnrollment $enrollment
     */
    public function updateLastActivity(TeachableEnrollment $enrollment)
    {
        if ($enrollment->getLastActivityAt()) {
            $this->lastActivityAt = $enrollment->getLastActivityAt();
        }

        if ($enrollment->getLastSignedAt()) {
            $this->lastSignInAt = $enrollment->getLastSignedAt();
        }
    }

    /**
     * @param TeachableLectureProgress $lectureProgress
     */
    public function updateLastSignIn(TeachableLectureProgress $lectureProgress)
    {
        $this->lastSignInAt = $lectureProgress->getLastSignedAt();
    }

    /**
     * @return string | null
     */
    public function getCourseName(): ?string
    {
        return $this->courseName;
    }

    /**
     * @param string $courseName
     */
    public function setCourseName(string $courseName): void
    {
        $this->courseName = $courseName;
    }

    /**
     * @param \DateTime|null $lastSignInAt
     */
    public function setLastSignInAt(?\DateTime $lastSignInAt): void
    {
        $this->lastSignInAt = $lastSignInAt;
    }


}
