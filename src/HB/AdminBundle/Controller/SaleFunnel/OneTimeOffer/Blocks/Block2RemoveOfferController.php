<?php

declare(strict_types = 1);

namespace HB\AdminBundle\Controller\SaleFunnel\OneTimeOffer\Blocks;

use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Component\Json;
use HB\AdminBundle\Entity\SaleFunnel\OneTimeOffer;
use HB\AdminBundle\Service\CourseAccessService;
use Symfony\Component\HttpFoundation\JsonResponse;

class Block2RemoveOfferController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var CourseAccessService
     */
    private $accessService;

    /**
     * Block2RemoveOfferController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param CourseAccessService    $accessService
     */
    public function __construct(EntityManagerInterface $entityManager, CourseAccessService $accessService)
    {
        $this->entityManager = $entityManager;
        $this->accessService = $accessService;
    }

    /**
     * @param OneTimeOffer $offer
     *
     * @return JsonResponse
     */
    public function handleAction(OneTimeOffer $offer)
    {
        $funnel = $offer->getFunnel();
        $this->accessService->resolveDeleteAccess($offer->getFunnel());

        $funnel->removeOffer($offer);
        $this->entityManager->remove($offer);
        $this->entityManager->persist($funnel);
        $this->entityManager->flush();

        return Json::ok();
    }
}