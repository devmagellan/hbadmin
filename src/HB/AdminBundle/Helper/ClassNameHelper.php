<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Helper;


class ClassNameHelper
{
    /**
     * @param string $namespace
     *
     * @return string
     */
    public static function getClassName(string $namespace)
    {
        $exploded = explode('\\', $namespace);

        return $exploded[\count($exploded) - 1];
    }
}