<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Helper;


class ArrayHelper
{
    public static function getArrayByIndex(array $array, string $index)
    {
        return array_map(function ($row) use ($index) {
            return $row[$index];
        }, $array);
    }
}