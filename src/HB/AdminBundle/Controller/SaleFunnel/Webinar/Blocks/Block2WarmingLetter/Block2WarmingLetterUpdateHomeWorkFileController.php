<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\Webinar\Blocks\Block2WarmingLetter;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelWebinar;
use HB\AdminBundle\Entity\UploadCareFile;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\UploadCareService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class Block2WarmingLetterUpdateHomeWorkFileController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var UploadCareService
     */
    private $uploadCareService;

    /**
     * @var CourseAccessService
     */
    private $courseAccessService;

    /**
     * Block2WarmingLetterUpdateHomeWorkFileController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param UploadCareService      $uploadCareService
     * @param CourseAccessService    $courseAccessService
     */
    public function __construct(EntityManagerInterface $entityManager, UploadCareService $uploadCareService, CourseAccessService $courseAccessService)
    {
        $this->entityManager = $entityManager;
        $this->uploadCareService = $uploadCareService;
        $this->courseAccessService = $courseAccessService;
    }


    /**
     * @param SalesFunnelWebinar $funnel
     * @param Request            $request
     *
     * @return JsonResponse
     */
    public function handleAction(SalesFunnelWebinar $funnel, Request $request)
    {
        $this->courseAccessService->resolveUpdateAccess($funnel);

        $fileUuid = $request->get('uuid');
        $fileCdn = $request->get('cdn');
        $fileName = $request->get('file_name');

        if ($fileUuid && $fileCdn && $fileName) {
            if ($funnel->getBlock2HomeworkFile()) {
                $this->uploadCareService->removeFile($funnel->getBlock2HomeworkFile()->getFileUuid());

                $this->entityManager->remove($funnel->getBlock2HomeworkFile());
            }
            $file = new UploadCareFile($fileUuid, $fileCdn, $fileName);

            $this->entityManager->persist($file);

            $funnel->setBlock2HomeworkFile($file);

            $this->entityManager->persist($funnel);

            $this->entityManager->flush();
            $this->courseAccessService->registerFileAddEvent($file, [
                'description' => 'Вебинарная воронка, блок 2, домашнее задание',
                'course'      => $funnel->getCourse()->getId(),
            ]);

            return new JsonResponse(['status' => 'success']);
        }

        return new JsonResponse(['status' => 'error']);
    }
}