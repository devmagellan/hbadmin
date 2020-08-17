<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\UploadCare;


use HB\AdminBundle\Service\UploadCareService;
use Symfony\Component\HttpFoundation\JsonResponse;

class RemoveFileController
{
    /**
     * @var UploadCareService
     */
    private $uploadCareService;

    /**
     * RemoveFileController constructor.
     *
     * @param UploadCareService $uploadCareService
     */
    public function __construct(UploadCareService $uploadCareService)
    {
        $this->uploadCareService = $uploadCareService;
    }

    /**
     * @param string $uuid
     *
     * @return JsonResponse
     */
    public function handleAction(string $uuid): JsonResponse
    {
        try {
            $this->uploadCareService->removeFile($uuid);
            return new JsonResponse(['status' => 'success']);
        } catch (\Exception $exception) {
            return new JsonResponse(['status' => 'error']);
        }
    }
}