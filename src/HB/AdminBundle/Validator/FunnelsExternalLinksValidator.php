<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Validator;


use HB\AdminBundle\Entity\Course;

class FunnelsExternalLinksValidator
{
    /**
     * @param Course $course
     *
     * @return array
     */
    public static function validate(Course $course): array
    {
        $errors = [];

        $postfix = ' для курса #' . $course->getId();

        if (!$course->getLinkOnlineSchool()) {
            $errors[] = 'Добавьте публичную ссылку для Онлайн школы'.$postfix;
        }

        if (!$course->getLinkPaymentPage()) {
            $errors[] = 'Добавьте публичную ссылку для Страницы Оплаты'.$postfix;
        }

        return $errors;
    }
}
