<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\HotLeads;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\SaleFunnel\HotLeads\SuccessHistory;
use HB\AdminBundle\Service\CourseAccessService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

class RemoveSuccessHistoryController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var CourseAccessService
     */
    private $access;

    /**
     * RemoveSuccessHistoryController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param RouterInterface        $router
     * @param CourseAccessService    $access
     */
    public function __construct(EntityManagerInterface $entityManager, RouterInterface $router, CourseAccessService $access)
    {
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->access = $access;
    }


    /**
     * @param SuccessHistory $history
     *
     * @return RedirectResponse
     */
    public function handleAction(SuccessHistory $history)
    {
        $this->access->resolveDeleteAccess($history->getFunnelHotLeads());

        $funnelId = $history->getFunnelHotLeads()->getId();

        $this->entityManager->remove($history);
        $this->entityManager->flush();

        return new RedirectResponse($this->router->generate('hb.sale_funnel.hot_leads.edit', ['id' => $funnelId]));
    }
}