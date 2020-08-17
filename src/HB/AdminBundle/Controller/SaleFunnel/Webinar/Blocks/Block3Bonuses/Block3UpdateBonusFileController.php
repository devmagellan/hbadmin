<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\Webinar\Blocks\Block3Bonuses;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelWebinar;
use HB\AdminBundle\Entity\SaleFunnel\Webinar\WebinarBonus;
use HB\AdminBundle\Entity\UploadCareFile;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\UploadCareService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class Block3UpdateBonusFileController
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
     * Block3UpdateBonusFileController constructor.
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
     * @param WebinarBonus $bonus
     * @param Request      $request
     *
     * @return JsonResponse
     */
    public function handleAction(WebinarBonus $bonus, Request $request)
    {
        $funnel = $this->getBonusFunnel($bonus);
        if ($funnel) {
            $this->courseAccessService->resolveUpdateAccess($funnel);
        }

        $fileUuid = $request->get('uuid');
        $fileCdn = $request->get('cdn');
        $fileName = $request->get('file_name');

        if ($fileUuid && $fileCdn && $fileName) {
            if ($bonus->getFile()) {
                $this->uploadCareService->removeFile($bonus->getFile()->getFileUuid());

                $this->entityManager->remove($bonus->getFile());
            }
            $file = new UploadCareFile($fileUuid, $fileCdn, $fileName);

            $this->entityManager->persist($file);

            $bonus->setFile($file);

            $this->entityManager->persist($bonus);

            $this->entityManager->flush();
            $this->courseAccessService->registerFileAddEvent($file, [
                'description' => 'Вебинарная воронка, блок 3, бонус',
                'course'      => $funnel->getCourse()->getId(),
            ]);

            return new JsonResponse(['status' => 'success']);
        }

        return new JsonResponse(['status' => 'error']);
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