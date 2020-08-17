<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * UploadCareFile
 *
 * This class contain info about uploaded file and can be used with any Entity with Unidirectional relation
 *
 * @ORM\Table(name="upload_care")
 * @ORM\Entity()
 */
class UploadCareFile
{
    public const SOURCE_UPLOADCARE = 'UPLOADCARE';
    public const SOURCE_DROPBOX = 'DROPBOX';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="file_uuid", type="string", length=255)
     */
    private $fileUuid;

    /**
     * @var string
     *
     * @ORM\Column(name="file_cdn", type="string", length=255)
     */
    private $fileUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="file_name", type="string")
     */
    private $fileName;

    /**
     * @var string
     *
     * @ORM\Column(name="source", type="string", nullable=true)
     */
    private $source;

    /**
     * @var string | null
     *
     * @ORM\Column(name="dropbox_async_job_id", type="string", nullable=true)
     */
    private $dropboxAsyncJobId;

    /**
     * @var string | null
     *
     * @ORM\Column(name="dropbox_file_path", type="string", nullable=true)
     */
    private $dropboxFilePath;

    /**
     * UploadCareFile constructor.
     *
     * @param string $fileUuid
     * @param string $fileUrl
     * @param string $fileName
     * @param string $source
     */
    public function __construct(string $fileUuid, string $fileUrl, string $fileName, string $source = self::SOURCE_UPLOADCARE)
    {
        $this->fileUuid = $fileUuid;
        $this->fileUrl = $fileUrl;
        $this->fileName = $fileName;

        if (!in_array($source, [self::SOURCE_UPLOADCARE, self::SOURCE_DROPBOX])) {
            throw new \InvalidArgumentException();
        }
        $this->source = $source;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getFileUrl(): string
    {
        return $this->fileUrl;
    }

    /**
     * @return string
     */
    public function getFileUuid(): string
    {
        return $this->fileUuid;
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * @return string | null
     */
    public function getSource(): ?string
    {
        return $this->source;
    }

    /**
     * @param string $fileUrl
     * @param string $fileId
     */
    public function switchSourceToDropBox(string $fileUrl, string $fileId)
    {
        $this->fileUrl = $fileUrl;
        $this->source = self::SOURCE_DROPBOX;
        $this->fileUuid = $fileId;
        $this->dropboxAsyncJobId = null;
    }

    /**
     * @param string $dropboxAsyncJobId
     * @param string $filePath
     */
    public function registerAsyncDropBoxJob(string $dropboxAsyncJobId, string $filePath)
    {
        $this->dropboxAsyncJobId = $dropboxAsyncJobId;
        $this->dropboxFilePath = $filePath;
    }

    /**
     * @return string|null
     */
    public function getDropboxAsyncJobId(): ?string
    {
        return $this->dropboxAsyncJobId;
    }

    /**
     * @return string|null
     */
    public function getDropboxFilePath(): ?string
    {
        return $this->dropboxFilePath;
    }

}