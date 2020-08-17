<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\MiniCourse;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\SaleFunnel\MiniCourse\MiniLesson;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelMiniCourse;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\UploadCareService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

class RemoveController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var UploadCareService
     */
    private $uploadCareService;

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
     * @param UploadCareService      $uploadCareService
     * @param RouterInterface        $router
     * @param CourseAccessService    $access
     */
    public function __construct(EntityManagerInterface $entityManager, UploadCareService $uploadCareService, RouterInterface $router, CourseAccessService $access)
    {
        $this->entityManager = $entityManager;
        $this->uploadCareService = $uploadCareService;
        $this->router = $router;
        $this->access = $access;
    }


    /**
     * @param SalesFunnelMiniCourse $funnel
     *
     * @return RedirectResponse
     */
    public function handleAction(SalesFunnelMiniCourse $funnel)
    {
        $this->access->resolveDeleteAccess($funnel);

        $course = $funnel->getCourse();

        /** @var MiniLesson $lesson */
        foreach ($funnel->getLessons() as $lesson) {
            if ($lesson->getLessonFile()) {
                $this->uploadCareService->removeFile($lesson->getLessonFile()->getFileUuid());
            }

            if ($lesson->getHomeWorkFile()) {
                $this->uploadCareService->removeFile($lesson->getHomeWorkFile()->getFileUuid());
            }
        }

        $course->setSalesFunnelMiniCourse(null);
        $this->entityManager->persist($course);
        $this->entityManager->remove($funnel);
        $this->entityManager->flush();

        return new RedirectResponse($this->router->generate('hb.course.edit', ['id' => $course->getId()]));
    }
}