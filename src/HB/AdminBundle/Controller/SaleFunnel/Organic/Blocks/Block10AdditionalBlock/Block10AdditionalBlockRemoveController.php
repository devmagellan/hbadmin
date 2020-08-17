<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\Organic\Blocks\Block10AdditionalBlock;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\AdditionalBlock;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelOrganic;
use HB\AdminBundle\Service\CourseAccessService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class Block10AdditionalBlockRemoveController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var CourseAccessService
     */
    private $courseAccessService;

    /**
     * Block10AdditionalBlockRemoveController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param CourseAccessService    $courseAccessService
     */
    public function __construct(EntityManagerInterface $entityManager, CourseAccessService $courseAccessService)
    {
        $this->entityManager = $entityManager;
        $this->courseAccessService = $courseAccessService;
    }


    /**
     * @param SalesFunnelOrganic $funnel
     * @param AdditionalBlock    $block
     *
     * @return Response
     */
    public function handleAction(SalesFunnelOrganic $funnel, AdditionalBlock $block)
    {
        $this->courseAccessService->resolveDeleteAccess($funnel);

        $funnel->removeBlock10AdditionalBlock($block);
        $this->entityManager->remove($block);
        $this->entityManager->persist($funnel);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'success']);
    }
}