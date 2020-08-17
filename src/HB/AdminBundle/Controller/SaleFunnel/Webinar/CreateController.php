<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\Webinar;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelWebinar;
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
    private $courseAccessService;

    /**
     * CreateController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param RouterInterface        $router
     * @param CourseAccessService    $courseAccessService
     */
    public function __construct(EntityManagerInterface $entityManager, RouterInterface $router, CourseAccessService $courseAccessService)
    {
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->courseAccessService = $courseAccessService;
    }


    /**
     * @param Course $course
     *
     * @return RedirectResponse
     */
    public function handleAction(Course $course)
    {
        $funnel = new SalesFunnelWebinar($course);

        $this->courseAccessService->resolveCreateAccess($course);

        $course->addSalesFunnelWebinar($funnel);

        $this->entityManager->persist($course);
        $this->entityManager->flush();

        return new RedirectResponse($this->router->generate('hb.sale_funnel.webinar.edit', ['id' => $funnel->getId()]));
    }

}