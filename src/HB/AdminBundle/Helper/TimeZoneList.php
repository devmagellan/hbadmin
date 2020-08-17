<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Helper;


class TimeZoneList
{
    /**
     * Returns a normalized array of timezone choices.
     *
     * @param $regions
     *
     * @return array|mixed
     * @throws \Exception
     */
    public static function getTimezones($regions)
    {
        $timezones = [];

        foreach (\DateTimeZone::listIdentifiers($regions) as $timezone) {
            $parts = explode('/', $timezone);

            if (\count($parts) > 2) {
                $region = $parts[0];
                $name = $parts[1].' - '.$parts[2];
            } else if (\count($parts) > 1) {
                $region = $parts[0];
                $name = $parts[1];
            } else {
                $region = 'Other';
                $name = $parts[0];
            }

            $temporaryTimeZone = new \DateTimeImmutable('now', new \DateTimeZone($timezone));
            $offset = $temporaryTimeZone->getOffset() / 3600;
            $offsetString = $offset > 0 ? ' (GMT +'.$offset.')' : ' (GMT '.$offset.')';

            $timezones[$region][str_replace('_', ' ', $name).$offsetString] = $timezone;
        }

        return 1 === \count($timezones) ? reset($timezones) : $timezones;
    }
}