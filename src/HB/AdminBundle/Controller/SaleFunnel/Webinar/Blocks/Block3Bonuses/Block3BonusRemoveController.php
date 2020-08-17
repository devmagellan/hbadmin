<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\Webinar\Blocks\Block3Bonuses;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelWebinar;
use HB\AdminBundle\Entity\SaleFunnel\Webinar\WebinarBonus;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\UploadCareService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class Block3BonusRemoveController
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
     * Block3BonusRemoveController constructor.
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
     * @param WebinarBonus       $bonus
     *
     * @return Response
     */
    public function handleAction(SalesFunnelWebinar $funnel, WebinarBonus $bonus)
    {
        $this->courseAccessService->resolveDeleteAccess($funnel);

        if ($bonus->getFile()) {
             $this->uploadCareService->removeFile($bonus->getFile()->getFileUuid());
        }

        $funnel->removeBonus($bonus);
        $this->entityManager->persist($funnel);
        $this->entityManager->remove($bonus);
        $this->entityManager->flush();


        return new JsonResponse(['status' => 'success']);
    }
}