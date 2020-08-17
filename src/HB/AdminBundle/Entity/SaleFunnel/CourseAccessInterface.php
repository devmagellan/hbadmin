<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Entity\SaleFunnel;


use HB\AdminBundle\Entity\Course;

interface CourseAccessInterface
{
    /**
     * @return Course
     */
    public function getCourse(): Course;
}