<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Service;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\UploadCareFile;
use Uploadcare\Api;
use Uploadcare\File;

class UploadCareService
{
    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var string
     */
    private $apiSecret;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * UploadCareService constructor.
     *
     * @param string                 $apiKey
     * @param string                 $apiSecret
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(string $apiKey, string $apiSecret, EntityManagerInterface $entityManager)
    {
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
        $this->entityManager = $entityManager;
    }

    /**
     * File uuid
     *
     * @param string $uuid
     *
     * @throws \Exception
     */
    public function removeFile(string $uuid): void
    {
        $fileExist = $this->entityManager->getRepository(UploadCareFile::class)->findOneBy(['fileUuid' => $uuid]);

        if ($fileExist) {
            if (UploadCareFile::SOURCE_UPLOADCARE === $fileExist->getSource()) {
                $this->remove($uuid);
            }
        } else {
            $this->remove($uuid);
        }
    }

    /**
     * @param string $uuid
     *
     * @throws \Exception
     */
    private function remove(string $uuid)
    {
        $api = new Api($this->apiKey, $this->apiSecret);
        $file = new File($uuid, $api);

        $file->delete();
    }
}