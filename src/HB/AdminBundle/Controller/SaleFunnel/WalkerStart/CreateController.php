<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\WalkerStart;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelWalkerStart;
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
    private $access;

    /**
     * CreateController constructor.
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
     * @param Course $course
     *
     * @return RedirectResponse
     */
    public function handleAction(Course $course)
    {
        $funnel = new SalesFunnelWalkerStart($course);

        $this->access->resolveCreateAccess($funnel);

        $course->setSalesFunnelWalkerStart($funnel);

        $this->entityManager->persist($funnel);
        $this->entityManager->flush();

        return new RedirectResponse($this->router->generate('hb.course.edit', ['id' => $course->getId()]));
    }
}
