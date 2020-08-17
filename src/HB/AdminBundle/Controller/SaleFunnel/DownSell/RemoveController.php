<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\DownSell;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelDownSell;
use HB\AdminBundle\Service\CourseAccessService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

class RemoveController
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
     * RemoveController constructor.
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
     * @param SalesFunnelDownSell $funnel
     *
     * @return RedirectResponse
     */
    public function handleAction(SalesFunnelDownSell $funnel)
    {
        $this->access->resolveDeleteAccess($funnel);

        $course = $funnel->getCourse();

        $course->removeSalesFunnelDownSell($funnel);
        $this->entityManager->remove($funnel);
        $this->entityManager->persist($course);
        $this->entityManager->flush();

        return new RedirectResponse($this->router->generate('hb.sale_funnel.down_sell.list', ['id' => $course->getId()]));
    }
}