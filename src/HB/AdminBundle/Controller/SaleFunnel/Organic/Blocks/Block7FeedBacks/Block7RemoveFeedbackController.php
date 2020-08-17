<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\Organic\Blocks\Block7FeedBacks;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\SaleFunnel\Organic\FeedBackVideo;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelOrganic;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\UploadCareService;
use Symfony\Component\HttpFoundation\JsonResponse;

class Block7RemoveFeedbackController
{
    /**
     * @var UploadCareService
     */
    private $uploadCareService;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var CourseAccessService
     */
    private $courseAccessService;

    /**
     * Block7RemoveFeedbackController constructor.
     *
     * @param UploadCareService      $uploadCareService
     * @param EntityManagerInterface $entityManager
     * @param CourseAccessService    $courseAccessService
     */
    public function __construct(UploadCareService $uploadCareService, EntityManagerInterface $entityManager, CourseAccessService $courseAccessService)
    {
        $this->uploadCareService = $uploadCareService;
        $this->entityManager = $entityManager;
        $this->courseAccessService = $courseAccessService;
    }


    /**
     * @param FeedBackVideo      $feedback
     * @param SalesFunnelOrganic $funnel
     *
     * @return JsonResponse
     */
    public function handleAction(FeedBackVideo $feedback, SalesFunnelOrganic $funnel)
    {
        $this->courseAccessService->resolveDeleteAccess($funnel);

        $blockVideo = $feedback->getFeedBackVideo();
        if ($blockVideo) {
            $this->uploadCareService->removeFile($blockVideo->getFileUuid());

            $feedback->setFeedBackVideo(null);
            $this->entityManager->persist($feedback);
            $this->entityManager->flush();
        }

        $funnel->removeBlock7Feedback($feedback);
        $this->entityManager->persist($funnel);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'success']);
    }
}