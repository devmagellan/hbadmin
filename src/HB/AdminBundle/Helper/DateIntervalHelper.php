<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Helper;


class DateIntervalHelper
{

    /**
     * Get user-friendly interval
     *
     * @param \DateTime|null $lastDate
     *
     * @return string
     * @throws \Exception
     */
    public static function getIntervalText(?\DateTime $lastDate)
    {
        $currentDate = new \DateTime();

        $interval = $currentDate->diff($lastDate);
        $str = '';

        if ($interval->i) {
            $str = $interval->i.' минут(ы)';
        }

        if ($interval->h) {
            $str = $interval->h.' часа(ов)';
        }

        if ($interval->d) {
            $str = $interval->d.' дня(ей)';
        }

        if ($interval->m) {
            $str = $interval->m.' месяца(ев)';
        }

        $str = strlen($str) > 0 ? $str . ' назад' : '-';

        return $str;
    }
}