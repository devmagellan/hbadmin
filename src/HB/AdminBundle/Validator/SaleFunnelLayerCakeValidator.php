<?php

declare(strict_types = 1);

namespace HB\AdminBundle\Validator;

use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelLayerCake;

class SaleFunnelLayerCakeValidator
{
    /**
     * Return array with error messages
     *
     * @param SalesFunnelLayerCake $funnel
     *
     * @return array
     */
    public static function validate(SalesFunnelLayerCake $funnel): array
    {
        $errors = [];
        $postfix = sprintf(' (id: %s)', $funnel->getId());

        if ($funnel->getLessons()->count() === 0) {
            $errors[] = 'Добавьте уроки в воронку'.$postfix;
        }

        try {
            self::checkForClone($funnel);
        } catch (\LogicException $exception) {
            $errors[] = $exception->getMessage();
        }

        return $errors;
    }

    /**
     * @param SalesFunnelLayerCake $currentFunnel
     *
     * @throws \LogicException
     */
    private static function checkForClone(SalesFunnelLayerCake $currentFunnel)
    {
        $funnels = $currentFunnel->getCourse()->getSaleFunnelLayerCakes();

        $currentFunnelLessonsIds = self::getLessonsIds($currentFunnel);

        /** @var SalesFunnelLayerCake $funnel */
        foreach ($funnels as $funnel) {

            if ($funnel->getId() !== $currentFunnel->getId() && $funnel->getTitle() === $currentFunnel->getTitle() && $funnel->getPrice() === $currentFunnel->getPrice()) {

                $sectionsIds = self::getLessonsIds($funnel);

                if ($sectionsIds == $currentFunnelLessonsIds) {
                    throw new \LogicException(sprintf('Воронка id: %s полностью идентичная воронке id: %s', $currentFunnel->getId(), $funnel->getId()));
                }
            }
        }
    }

    /**
     * @param SalesFunnelLayerCake $funnel
     *
     * @return array
     */
    private static function getLessonsIds(SalesFunnelLayerCake $funnel)
    {
        $ids = [];

        foreach ($funnel->getLessons() as $lesson) {
            $ids[] = $lesson->getId();
        }

        sort($ids);

        return $ids;
    }
}