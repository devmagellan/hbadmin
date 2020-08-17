<?php

declare(strict_types = 1);

namespace HB\AdminBundle\Validator;

use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelOrganic;

class SaleFunnelOrganicValidator
{
    /**
     * Return array with error messages
     *
     * @param SalesFunnelOrganic $funnel
     *
     * @return array
     */
    public static function validate(SalesFunnelOrganic $funnel): array
    {
        $errors = [];

        if (!$funnel->getBlock2CourseInfo()) {
            $errors[] = 'Добавьте текст описания (Органическая воронка: Блок 2)';
        }

        if (\count($funnel->getBlock3KnowledgeSkills()) !== 3 && \count($funnel->getBlock3KnowledgeSkills()) !== 6) {
            $errors[] = 'Необходимо указать 3 или 6 навыков (Органическая воронка: Блок 3)';
        }

        if (!$funnel->getBlock5Type()) {
            $errors[] = 'Добавьте тип описания (Органическая воронка: Блок 5)';
        }

        if (!$funnel->getBlock5Text()) {
            $errors[] = 'Добавьте описание (Органическая воронка: Блок 5)';
        }

        if (!$funnel->getBlock6AuthorInfo()) {
            $errors[] = 'Добавьте информацию об авторе (Органическая воронка: Блок 6)';
        }

        if (!$funnel->getBlock6AuthorExperience()) {
            $errors[] = 'Добавьте информацию об опыте авторе (Органическая воронка: Блок 6)';
        }

        if (!$funnel->getBlock6AuthorPhoto()) {
            $errors[] = 'Добавьте фотографию автора (Органическая воронка: Блок 6)';
        }

        return $errors;
    }
}