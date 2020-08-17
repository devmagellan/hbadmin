<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\Webinar\Blocks\Block3Bonuses;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelWebinar;
use HB\AdminBundle\Entity\SaleFunnel\Webinar\WebinarBonus;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\UploadCareService;
use Symfony\Component\HttpFoundation\JsonResponse;

class Block3RemoveBonusFileController
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
     * Block2WarmingLetterRemoveWorkBookFileController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param UploadCareService      $uploadCareService
     */
    public function __construct(EntityManagerInterface $entityManager, UploadCareService $uploadCareService)
    {
        $this->entityManager = $entityManager;
        $this->uploadCareService = $uploadCareService;
    }

    /**
     * @param WebinarBonus $bonus
     *
     * @return JsonResponse
     */
    public function handleAction(WebinarBonus $bonus)
    {
        $funnel = $this->getBonusFunnel($bonus);
        if ($funnel) {
            $this->courseAccessService->resolveDeleteAccess($funnel);
        }

        $file = $bonus->getFile();
        if ($file) {
            $this->uploadCareService->removeFile($file->getFileUuid());

            $this->entityManager->remove($file);
            $bonus->setFile(null);
            $this->entityManager->persist($bonus);
            $this->entityManager->flush();
        }

        return new JsonResponse(['status' => 'success']);
    }

    /**
     * @param WebinarBonus $bonus
     *
     * @return mixed
     */
    private function getBonusFunnel(WebinarBonus $bonus): ?SalesFunnelWebinar
    {
        return $this->entityManager->createQueryBuilder()
            ->select('funnel')
            ->from(SalesFunnelWebinar::class, 'funnel')
            ->leftJoin('funnel.bonuses', 'bonus')
            ->where('bonus.id = :bonus_id')
            ->setParameter('bonus_id', $bonus->getId())
            ->getQuery()->getOneOrNullResult();
    }
}