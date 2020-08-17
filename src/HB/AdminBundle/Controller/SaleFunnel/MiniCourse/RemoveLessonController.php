<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\MiniCourse;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\SaleFunnel\MiniCourse\MiniLesson;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\UploadCareService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

class RemoveLessonController
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
     * RemoveLessonController constructor.
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
     * @param MiniLesson $lesson
     *
     * @return RedirectResponse
     */
    public function handleAction(MiniLesson $lesson)
    {
        $this->access->resolveDeleteAccess($lesson->getFunnelMiniCourse());

        $courseId = $lesson->getFunnelMiniCourse()->getCourse()->getId();

        if ($lesson->getLessonFile()) {
            $this->uploadCareService->removeFile($lesson->getLessonFile()->getFileUuid());
        }

        if ($lesson->getHomeWorkFile()) {
            $this->uploadCareService->removeFile($lesson->getHomeWorkFile()->getFileUuid());
        }

        $this->entityManager->remove($lesson);
        $this->entityManager->flush();

        return new RedirectResponse($this->router->generate('hb.sale_funnel.mini_course.edit', ['id' => $courseId]));
    }
}