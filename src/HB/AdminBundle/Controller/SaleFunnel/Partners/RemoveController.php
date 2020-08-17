<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\Partners;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelPartner;
use HB\AdminBundle\Service\CourseAccessService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

class RemoveController
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var CourseAccessService
     */
    private $accessService;

    /**
     * RemoveController constructor.
     *
     * @param RouterInterface        $router
     * @param EntityManagerInterface $entityManager
     * @param CourseAccessService    $accessService
     */
    public function __construct(RouterInterface $router, EntityManagerInterface $entityManager, CourseAccessService $accessService)
    {
        $this->router = $router;
        $this->entityManager = $entityManager;
        $this->accessService = $accessService;
    }

    /**
     * @param SalesFunnelPartner $funnel
     *
     * @return RedirectResponse
     */
    public function handleAction(SalesFunnelPartner $funnel)
    {
        $this->accessService->resolveDeleteAccess($funnel);

        $courseId = $funnel->getCourse()->getId();

        $this->entityManager->remove($funnel);
        $this->entityManager->flush();

        return new RedirectResponse($this->router->generate('hb.sale_funnel.partner.list', ['id' => $courseId]));
    }
}