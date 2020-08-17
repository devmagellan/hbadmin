<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\Organic\Blocks\Block7FeedBacks;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\SaleFunnel\Organic\FeedBackVideo;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelOrganic;
use HB\AdminBundle\Entity\UploadCareFile;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\UploadCareService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class Block7UpdateFeedbackVideoController
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
     * Block7UpdateFeedbackVideoController constructor.
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
     * @param FeedBackVideo $feedback
     * @param Request       $request
     *
     * @return JsonResponse
     */
    public function handleAction(FeedBackVideo $feedback, Request $request)
    {
        $funnel = $this->getFeedBackFunnel($feedback);
        if ($funnel) {
            $this->courseAccessService->resolveUpdateAccess($funnel);
        }

        $fileUuid = $request->get('uuid');
        $fileCdn = $request->get('cdn');
        $fileName = $request->get('file_name');

        if ($fileUuid && $fileCdn && $fileName) {
            if ($feedback->getFeedBackVideo()) {
                $this->uploadCareService->removeFile($feedback->getFeedBackVideo()->getFileUuid());

                $this->entityManager->remove($feedback->getFeedBackVideo());
            }
            $file = new UploadCareFile($fileUuid, $fileCdn, $fileName);

            $this->entityManager->persist($file);

            $feedback->setFeedBackVideo($file);

            $this->entityManager->persist($feedback);

            $this->entityManager->flush();
            $this->courseAccessService->registerFileAddEvent($file, [
                'description' => 'Органическая воронка, блок 7, фидбек',
                'course'      => $funnel->getCourse()->getId(),
            ]);

            return new JsonResponse(['status' => 'success']);
        }
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