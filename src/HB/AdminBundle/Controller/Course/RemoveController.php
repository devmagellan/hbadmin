<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\Course;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Entity\SaleFunnelOrganicPriceBlockSetting;
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
    private $courseAccessService;

    /**
     * RemoveController constructor.
     *
     * @param RouterInterface        $router
     * @param EntityManagerInterface $entityManager
     * @param CourseAccessService    $courseAccessService
     */
    public function __construct(RouterInterface $router, EntityManagerInterface $entityManager, CourseAccessService $courseAccessService)
    {
        $this->router = $router;
        $this->entityManager = $entityManager;
        $this->courseAccessService = $courseAccessService;
    }

    /**
     * @param Course $course
     *
     * @return RedirectResponse
     */
    public function handleAction(Course $course): RedirectResponse
    {
        $this->courseAccessService->resolveDeleteAccess($course);

        $saleFunnelOrganic = $course->getSalesFunnelOrganic();
        if ($saleFunnelOrganic) {

            foreach ($saleFunnelOrganic->getPriceBlocks() as $priceBlock) {
                $saleFunnelOrganic->removePriceBlock($priceBlock);
            }
            $this->entityManager->persist($saleFunnelOrganic);
            $this->entityManager->flush();
        }

        $this->removeCourseOrganicFunnelPriceBlocks($course);
        $this->entityManager->remove($course);
        $this->entityManager->flush();

        return new RedirectResponse($this->router->generate('hb.courses.list'));
    }

    /**
     * @param Course $course
     */
    private function removeCourseOrganicFunnelPriceBlocks(Course $course)
    {
        $saleFunnelOrganicPriceBlockSettings = $this->entityManager->createQueryBuilder()
            ->select('price_block')
            ->from(SaleFunnelOrganicPriceBlockSetting::class, 'price_block')
            ->leftJoin('price_block.funnel', 'organic_funnel')
            ->where('organic_funnel.course = :course')
            ->setParameter('course', $course)
            ->getQuery()
            ->getResult();

        /** @var SaleFunnelOrganicPriceBlockSetting $priceBlockSetting */
        foreach ($saleFunnelOrganicPriceBlockSettings as $priceBlockSetting) {
            $additionalCourses = $priceBlockSetting->getCourses();

            foreach ($additionalCourses as $additionalCourse) {
              $priceBlockSetting->removeCourse($additionalCourse);
            }
            $this->entityManager->persist($priceBlockSetting);
            $this->entityManager->remove($priceBlockSetting);
        }
    }

}
