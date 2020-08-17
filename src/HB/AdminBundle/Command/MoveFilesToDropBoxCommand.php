<?php

namespace HB\AdminBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\UploadCareFile;
use HB\AdminBundle\Service\FileMovingService;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class MoveFilesToDropBoxCommand extends ContainerAwareCommand
{
    private const COMMAND_NAME = 'files:move:dropbox';

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var FileMovingService
     */
    private $fileMovingService;

    protected function configure()
    {
        $this->setName(self::COMMAND_NAME)
            ->addOption("file_uuid", null, InputOption::VALUE_OPTIONAL, "You can set specific file uuid.")
            ->addOption("limit", null, InputOption::VALUE_OPTIONAL, "Limit of files to move");
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|void|null
     * @throws \Kunnu\Dropbox\Exceptions\DropboxClientException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->entityManager = $this->getContainer()->get('doctrine.orm.entity_manager');
        $this->fileMovingService = $this->getContainer()->get('file_moving.service');

        $fileUuid = $input->getOption("file_uuid");
        $limit = $input->getOption('limit') ? (int) $input->getOption("limit") : 10;

        $files = [];

        if ($fileUuid) {
            $file = $this->entityManager->getRepository(UploadCareFile::class)->findOneBy(['fileUuid' => $fileUuid]);
            if ($file) {
                $files[] = $file;
            }
        } else {
            // temporary disabled
//            $files = $this->getUploadCareFiles($limit);
        }

        $output->writeln([sprintf('Total files count %s', \count($files))]);

        /** @var UploadCareFile $file */
        foreach ($files as $file) {
            $this->fileMovingService->startDropboxAsyncJob($file);

            $output->writeln([sprintf('Registered Dropbox async job for file id: %s', $file->getId())]);
        }

        $output->writeln([sprintf('Dropbox async jobs registered.')]);

        $this->checkDropBoxJobs($output, $fileUuid);
    }

    /**
     * @param OutputInterface $output
     * @param string | null   $uuid
     *
     * @throws \Kunnu\Dropbox\Exceptions\DropboxClientException
     */
    public function checkDropBoxJobs(OutputInterface $output, string $uuid = null)
    {
        $movingFiles = [];

        if ($uuid) {
            $movingFiles = $this->entityManager->getRepository(UploadCareFile::class)->createQueryBuilder('f')
                ->where('f.source = :upload_care_source OR f.source IS NULL')
                ->andWhere('f.dropboxAsyncJobId IS NOT NULL')
                ->andWhere('f.fileUuid = :file_uuid')
                ->setParameters([
                    'upload_care_source' => UploadCareFile::SOURCE_UPLOADCARE,
                    'file_uuid' => $uuid
                ])
                ->getQuery()->getResult();
        } else {
            // temporary disabled
            /*$movingFiles = $this->entityManager->getRepository(UploadCareFile::class)->createQueryBuilder('f')
                ->where('f.source = :upload_care_source OR f.source IS NULL')
                ->andWhere('f.dropboxAsyncJobId IS NOT NULL')
                ->setParameter('upload_care_source', UploadCareFile::SOURCE_UPLOADCARE)
                ->getQuery()->getResult();    */
        }

        $output->writeln([sprintf('Total file count for dropbox job check %s', \count($movingFiles))]);

        /** @var UploadCareFile $file */
        foreach ($movingFiles as $file) {
            $this->fileMovingService->checkDropBoxJob($file);
            $output->writeln([sprintf('Checked file id: %s', $file->getId())]);
        }

        $output->writeln(['Dropbox jobs checked.']);
    }

    /**
     * @param  int $limit
     *
     * @return mixed
     */
    private function getUploadCareFiles(int $limit = 10)
    {
        $files = $this->getContainer()->get('doctrine.orm.entity_manager')->getRepository(UploadCareFile::class)
            ->createQueryBuilder('file')
            ->where('file.source = :source OR file.source IS NULL')
            ->andWhere('file.dropboxAsyncJobId IS NULL')
            ->setParameter('source', UploadCareFile::SOURCE_UPLOADCARE);

        if ($limit) {
            $files->setMaxResults($limit);
        }

        return $files->getQuery()->getResult();
    }

}