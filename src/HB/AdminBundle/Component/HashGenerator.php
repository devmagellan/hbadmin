<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Component;

/**
 * Class HashGenerator
 *
 * Generate random md5 hash
 */
class HashGenerator
{
    public static function generate(int $length = 32)
    {
        return md5(bin2hex(random_bytes($length)));
    }
}