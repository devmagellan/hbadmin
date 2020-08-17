<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\Organic\Blocks\Block7FeedBacks;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\SaleFunnel\Organic\FeedBackVideo;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelOrganic;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\UploadCareService;
use Symfony\Component\HttpFoundation\JsonResponse;

class Block7RemoveFeedbackVideo
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
     * Block7RemoveFeedbackVideo constructor.
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
     * @param FeedBackVideo $feedback
     *
     * @return JsonResponse
     */
    public function handleAction(FeedBackVideo $feedback)
    {
        $funnel = $this->getFeedBackFunnel($feedback);
        if ($funnel) {
            $this->courseAccessService->resolveDeleteAccess($funnel);
        }

        $blockVideo = $feedback->getFeedBackVideo();
        if ($blockVideo) {
            $this->uploadCareService->removeFile($blockVideo->getFileUuid());

            $feedback->setFeedBackVideo(null);
            $this->entityManager->persist($feedback);
            $this->entityManager->flush();
        }

        return new JsonResponse(['status' => 'success']);
    }

    /**
     * @param FeedBackVideo $feedBackVideo
     *
     * @return SalesFunnelOrganic
     */
    private function getFeedBackFunnel(FeedBackVideo $feedBackVideo): SalesFunnelOrganic
    {
        return $this->entityManager->createQueryBuilder()
            ->select('organic_funnel')
            ->from(SalesFunnelOrganic::class, 'organic_funnel')
            ->leftJoin('organic_funnel.block7Feedbacks', 'feedback')
            ->where('feedback.id = :feedback_id')
            ->setParameter('feedback_id', $feedBackVideo->getId())
            ->getQuery()->getOneOrNullResult();
    }
}