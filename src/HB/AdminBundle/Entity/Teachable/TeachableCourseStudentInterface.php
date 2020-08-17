<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Entity\Teachable;


interface TeachableCourseStudentInterface
{
    /**
     * @return string
     */
    public function getStudentName(): string;

    /**
     * @return string
     */
    public function getStudentEmail(): string;

    /**
     * @return int
     */
    public function getCourseId(): int;

    /**
     * @return string | null
     */
    public function getCourseName(): ?string;
}