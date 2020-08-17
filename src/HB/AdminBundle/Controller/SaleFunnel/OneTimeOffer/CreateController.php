<?php

declare(strict_types = 1);

namespace HB\AdminBundle\Controller\SaleFunnel\OneTimeOffer;

use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelOneTimeOffer;
use HB\AdminBundle\Service\CourseAccessService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

class CreateController
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
    private $accessService;

    /**
     * CreateController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param RouterInterface        $router
     * @param CourseAccessService    $accesService
     */
    public function __construct(EntityManagerInterface $entityManager, RouterInterface $router, CourseAccessService $accesService)
    {
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->accessService = $accesService;
    }

    /**
     * @param Course $course
     */
    public function handleAction(Course $course)
    {
        $this->accessService->resolveCreateAccess($course);

        $funnel = new SalesFunnelOneTimeOffer($course);
        $course->addSalesFunnelOneTimeOffer($funnel);

        $this->entityManager->persist($funnel);
        $this->entityManager->flush();

        return new RedirectResponse($this->router->generate('hb.sale_funnel.one_time_offer.edit', [
            'id' => $funnel->getId()
        ]));
    }
}