<?php

declare(strict_types=1);

namespace HB\AdminBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class ImageManager
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var string
     */
    private $imageDir;

    /**
     * ImageManager constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param string $imageDir
     */
    public function __construct(EntityManagerInterface $entityManager, string $imageDir)
    {
        $this->entityManager = $entityManager;
        $this->imageDir = $imageDir;
    }


    /**
     * @param string $filePath
     *
     * @return bool
     */
    public function checkImage(string $filePath): bool
    {
        if (!is_file($filePath)) {
            return false;
        }

        $imageInfo = getimagesize($filePath);

        if ($imageInfo == false) {
            return false;
        }

        if (($imageInfo[2] !== IMAGETYPE_GIF) &&
            ($imageInfo[2] !== IMAGETYPE_JPEG) &&
            ($imageInfo[2] !== IMAGETYPE_PNG)) {
            return false;
        }

        return true;
    }

    /**
     * @param UploadedFile $image
     * @param string | null $oldImageName
     *
     * @return string
     */
    public function saveImage(UploadedFile $image, string $oldImageName = null): string
    {
        $fileName = $this->generateUniqueFileName() . '.' . $image->getClientOriginalExtension();

        if (!is_dir($this->getImageDir())) {
            mkdir($this->getImageDir());
        }

        $image->move($this->getImageDir(), $fileName);

        if ($oldImageName) {
            $this->unlinkImage($oldImageName);
        }

        return $fileName;
    }

    /**
     * @param UploadedFile $image
     * @param string|null  $oldImageName
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    public function saveImageWithCheck(UploadedFile $image, string $oldImageName = null): string
    {
        if (!$image->isValid()) {
            throw new \InvalidArgumentException('Ошибка загрузки');
        }

        if (!$this->checkImage($image->getRealPath())) {
            throw new \InvalidArgumentException('Неверный формат картинки');
        }

        return $this->saveImage($image, $oldImageName);
    }

    /**
     * @param string $imageName
     *
     * @return void
     */
    public function unlinkImage($imageName): void
    {
        @unlink($this->getImageDir() . $imageName);
    }

    /**
     * @return string
     */
    public function generateUniqueFileName(): string
    {
        return md5(uniqid());
    }

    /**
     * @return string
     */
    public function getImageDir(): string
    {
        return $this->imageDir;
    }

}