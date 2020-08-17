<?php

declare(strict_types = 1);

namespace HB\AdminBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\UploadCareFile;
use Kunnu\Dropbox\Dropbox;
use Kunnu\Dropbox\DropboxApp;
use Kunnu\Dropbox\Exceptions\DropboxClientException;
use Kunnu\Dropbox\Models\FileMetadata;

class FileMovingService
{
    /**
     * @var string
     */
    private $dropBoxAppKey;

    /**
     * @var string
     */
    private $dropBoxAppSecret;

    /**
     * @var string
     */
    private $dropBoxAccessToken;

    /**
     * @var UploadCareService
     */
    private $uploadCareService;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var Dropbox
     */
    private $dropbox;

    /**
     * FileMovingService constructor.
     *
     * @param string                 $dropBoxAppKey
     * @param string                 $dropBoxAppSecret
     * @param string                 $dropBoxAccessToken
     * @param UploadCareService      $uploadCareService
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(string $dropBoxAppKey, string $dropBoxAppSecret, $dropBoxAccessToken, UploadCareService $uploadCareService, EntityManagerInterface $entityManager)
    {
        $this->dropBoxAppKey = $dropBoxAppKey;
        $this->dropBoxAppSecret = $dropBoxAppSecret;
        $this->dropBoxAccessToken = $dropBoxAccessToken;
        $this->uploadCareService = $uploadCareService;
        $this->entityManager = $entityManager;

        $app = new DropboxApp($this->dropBoxAppKey, $this->dropBoxAppSecret, $this->dropBoxAccessToken);

        $this->dropbox = new Dropbox($app);
    }

    /**
     * @param UploadCareFile $file
     *
     * @throws DropboxClientException
     */
    public function startDropboxAsyncJob(UploadCareFile $file)
    {
        if (UploadCareFile::SOURCE_DROPBOX === $file->getSource()) {
            throw new \InvalidArgumentException(sprintf('File has already marked with dropbox source. Id: %s', $file->getId()));
        } else {

            $filePath = '/hbabmin/'.$file->getFileUuid().'_'.$file->getFileName();

            $dropboxAsyncJobId = $this->dropbox->saveUrl($filePath, $file->getFileUrl());

            $file->registerAsyncDropBoxJob($dropboxAsyncJobId, $filePath);
            $this->entityManager->persist($file);
            $this->entityManager->flush();
        }
    }

    /**
     * @param UploadCareFile $file
     *
     * @throws DropboxClientException
     */
    public function checkDropBoxJob(UploadCareFile $file)
    {
        if (!$file->getDropboxAsyncJobId()) {
            throw new \InvalidArgumentException(sprintf('File id: %s has no dropbox job id', $file->getId()));
        }

        $jobInfo = $this->dropbox->checkJobStatus($file->getDropboxAsyncJobId());

        if ($jobInfo instanceof FileMetadata) {
            $data = $jobInfo->getData();

            if (isset($data['.tag']) && $data['.tag'] === 'complete') {
                $uploadCareFileUuid = $file->getFileUuid();

                try {
                    $link = $this->getRecordLink($data['path_display']);
                    $file->switchSourceToDropBox($link, $jobInfo->getId());

                    $this->entityManager->persist($file);
                    $this->entityManager->flush();

                    // temporary disabled
//                    $this->uploadCareService->removeFile($uploadCareFileUuid);
                } catch (DropboxClientException $exception) {
                    // todo: shared link cannot be create twice. must get exist link
                }
            }
        }
    }

    /**
     * @param string $filename
     *
     * @return string
     */
    private function getRecordLink(string $filename)
    {
        $pathToFile = $filename;

        $response = $this->dropbox->postToAPI("/sharing/create_shared_link_with_settings", [
            "path" => $pathToFile,
        ]);

        $data = $response->getDecodedBody();
        $recordLink = str_replace('?dl=0', '?dl=1', $data['url']);

        return $recordLink;
    }
}
