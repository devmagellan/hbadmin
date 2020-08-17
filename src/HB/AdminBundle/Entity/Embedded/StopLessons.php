<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Entity\Embedded;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Embeddable
 */
class StopLessons
{
    /**
     * @var bool
     *
     * @ORM\Column(name="previous_lessons_mark", type="boolean")
     */
    private $previousLessonsMark = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="viewed_videos_mark", type="boolean")
     */
    private $viewedVideosMark = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="test_passed_mark", type="boolean")
     */
    private $testPassedMark = false;

    /**
     * @var float | null
     *
     * @Assert\Range(min="0", max="100",
     *     minMessage="Минимальное значение {{ limit }} %",
     *     maxMessage="Максимальное значение {{ limit }} %",
     * )
     *
     * @ORM\Column(name="tests_min_percent_passed", type="float", nullable=true)
     */
    private $testsMinPercentPassed;

    /**
     * @var int | null
     *
     * @Assert\Range(min="0", max="99",
     *     minMessage="Минимальное значение {{ limit }} %",
     *     maxMessage="Максимальное значение {{ limit }} %",
     * )
     *
     * @ORM\Column(name="tests_max_attempts", type="integer", nullable=true)
     */
    private $testsMaxAttempts;

    /**
     * @return bool
     */
    public function isPreviousLessonsMark(): bool
    {
        return $this->previousLessonsMark;
    }

    /**
     * @param bool $previousLessonsMark
     */
    public function setPreviousLessonsMark(bool $previousLessonsMark): void
    {
        $this->previousLessonsMark = $previousLessonsMark;
    }

    /**
     * @return bool
     */
    public function isViewedVideosMark(): bool
    {
        return $this->viewedVideosMark;
    }

    /**
     * @param bool $viewedVideosMark
     */
    public function setViewedVideosMark(bool $viewedVideosMark): void
    {
        $this->viewedVideosMark = $viewedVideosMark;
    }

    /**
     * @return bool
     */
    public function isTestPassedMark(): bool
    {
        return $this->testPassedMark;
    }

    /**
     * @param bool $testPassedMark
     */
    public function setTestPassedMark(bool $testPassedMark): void
    {
        $this->testPassedMark = $testPassedMark;
    }

    /**
     * @return float|null
     */
    public function getTestsMinPercentPassed(): ?float
    {
        return $this->testsMinPercentPassed;
    }

    /**
     * @param float|null $testsMinPercentPassed
     */
    public function setTestsMinPercentPassed(?float $testsMinPercentPassed): void
    {
        $this->testsMinPercentPassed = $testsMinPercentPassed;
    }

    /**
     * @return int|null
     */
    public function getTestsMaxAttempts(): ?int
    {
        return $this->testsMaxAttempts;
    }

    /**
     * @param int|null $testsMaxAttempts
     */
    public function setTestsMaxAttempts(?int $testsMaxAttempts): void
    {
        $this->testsMaxAttempts = $testsMaxAttempts;
    }
}
