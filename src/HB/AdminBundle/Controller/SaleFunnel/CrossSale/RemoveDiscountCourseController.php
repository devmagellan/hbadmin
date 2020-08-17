<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\CrossSale;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\SaleFunnel\CrossSale\DiscountCourse;
use HB\AdminBundle\Service\CourseAccessService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

class RemoveDiscountCourseController
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
     * RemoveDiscountCourseController constructor.
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
     * @param DiscountCourse $discountCourse
     *
     * @return RedirectResponse
     */
    public function handleAction(DiscountCourse $discountCourse)
    {
        $this->access->resolveDeleteAccess($discountCourse->getFunnelCrossSale());

        $courseId = $discountCourse->getFunnelCrossSale()->getCourse()->getId();

        $this->entityManager->remove($discountCourse);
        $this->entityManager->flush();

        return new RedirectResponse($this->router->generate('hb.sale_funnel.cross_sale.edit', ['id' => $courseId]));
    }
}