<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\MiniCourse;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelMiniCourse;
use HB\AdminBundle\Service\CourseAccessService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

class CreateController
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
    private $access;

    /**
     * CreateController constructor.
     *
     * @param RouterInterface        $router
     * @param EntityManagerInterface $entityManager
     * @param CourseAccessService    $access
     */
    public function __construct(RouterInterface $router, EntityManagerInterface $entityManager, CourseAccessService $access)
    {
        $this->router = $router;
        $this->entityManager = $entityManager;
        $this->access = $access;
    }

    /**
     * @param Course $course
     *
     * @return RedirectResponse
     */
    public function handleAction(Course $course)
    {
        $funnel = new SalesFunnelMiniCourse($course);

        $this->access->resolveCreateAccess($funnel);

        $this->entityManager->persist($funnel);

        $course->setSalesFunnelMiniCourse($funnel);
        $this->entityManager->persist($course);

        $this->entityManager->flush();

        return new RedirectResponse($this->router->generate('hb.sale_funnel.mini_course.edit', ['id' => $course->getId()]));
    }
}