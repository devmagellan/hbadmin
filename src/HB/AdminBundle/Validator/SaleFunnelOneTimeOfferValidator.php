<?php

declare(strict_types = 1);

namespace HB\AdminBundle\Validator;

use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelOneTimeOffer;

class SaleFunnelOneTimeOfferValidator
{
    /**
     * Return array with error messages
     *
     * @param SalesFunnelOneTimeOffer $funnel
     *
     * @return array
     */
    public static function validate(SalesFunnelOneTimeOffer $funnel): array
    {
        $errors = [];
//        $postFix = sprintf(' (id предложения: %s)', $funnel->getId());
        $postFix = '';

        if (!$funnel->isForEducational() && !$funnel->isForLeadMagnet() && !$funnel->isForMiniCourse() && !$funnel->isForWebinar()) {
            $errors[] = 'Необходимо выбрать хотя бы одну воронку в таргетинге предложения (Блок 1) ' . $postFix;
        }

        if (!$funnel->getOffers()->count()) {
            $errors[] = 'Добавьте продукт, который будете продавать на странице единоразового предложения (Блок 2) '.$postFix;
        }

        if (!strlen($funnel->getDescription())) {
            $errors[] = 'Добавитье Введение (Блок 3) '.$postFix;
        }

        if (!$funnel->getOfferFile() && !$funnel->getLink()) {
            $errors[] = 'Добавьте файл или ссылку того, что получат пользователи единоразового предложения (Блок 4) '.$postFix;
        }

        return $errors;
    }
}