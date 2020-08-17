<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\Organic\PriceBlock;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\SaleFunnelOrganicPriceBlockThesis;
use HB\AdminBundle\Service\CourseAccessService;
use Symfony\Component\HttpFoundation\JsonResponse;

class RemovePriceBlockSettingThesisController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var CourseAccessService
     */
    private $access;

    /**
     * RemovePriceBlockSettingThesisController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param CourseAccessService    $access
     */
    public function __construct(EntityManagerInterface $entityManager, CourseAccessService $access)
    {
        $this->entityManager = $entityManager;
        $this->access = $access;
    }


    /**
     * @param SaleFunnelOrganicPriceBlockThesis $thesis
     *
     * @return JsonResponse
     */
    public function handleAction(SaleFunnelOrganicPriceBlockThesis $thesis)
    {
        $this->access->resolveDeleteAccess($thesis->getSaleFunnelOrganicPriceBlockSetting()->getFunnel());

        $this->entityManager->remove($thesis);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'success']);
    }
}