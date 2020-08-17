<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Component;


use Symfony\Component\HttpFoundation\JsonResponse;

class Json
{
    /**
     * @param string $message
     *
     * @return JsonResponse
     */
    public static function ok(string $message = ''): JsonResponse
    {
        return new JsonResponse(['status' => 'success', 'message' => $message]);
    }

    /**
     * @param string $message
     *
     * @return JsonResponse
     */
    public static function error(string $message = ''): JsonResponse
    {
        return new JsonResponse(['status' => 'error', 'message' => $message]);
    }
}