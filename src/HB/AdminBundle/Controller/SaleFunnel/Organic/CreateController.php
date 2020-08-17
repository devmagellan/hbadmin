<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\Organic;


use Doctrine\ORM\EntityManager;
use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelOrganic;
use HB\AdminBundle\Service\CourseAccessService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

class CreateController
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var CourseAccessService
     */
    private $courseAccess;

    /**
     * CreateController constructor.
     *
     * @param EntityManager       $entityManager
     * @param RouterInterface     $router
     * @param CourseAccessService $courseAccess
     */
    public function __construct(EntityManager $entityManager, RouterInterface $router, CourseAccessService $courseAccess)
    {
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->courseAccess = $courseAccess;
    }

    /**
     * @param Course $course
     */
    public function handleAction(Course $course)
    {
        $organicFunnel = new SalesFunnelOrganic($course);

        $this->courseAccess->resolveCreateAccess($organicFunnel);

        $course->setSalesFunnelOrganic($organicFunnel);

        $this->entityManager->persist($course);
        $this->entityManager->flush();

        return new RedirectResponse($this->router->generate('hb.sale_funnel.organic.edit', ['id' => $course->getId()]));
    }
}