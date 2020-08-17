<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\MiniCourse;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\SaleFunnel\MiniCourse\MiniLesson;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\UploadCareService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

class RemoveFileLessonController
{
    /**
     * @var UploadCareService
     */
    private $uploadCareService;

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
     * RemoveFileLessonController constructor.
     *
     * @param UploadCareService      $uploadCareService
     * @param EntityManagerInterface $entityManager
     * @param RouterInterface        $router
     * @param CourseAccessService    $access
     */
    public function __construct(UploadCareService $uploadCareService, EntityManagerInterface $entityManager, RouterInterface $router, CourseAccessService $access)
    {
        $this->uploadCareService = $uploadCareService;
        $this->entityManager = $entityManager;
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
        $funnel = $lesson->getFunnelMiniCourse();
        $this->access->resolveDeleteAccess($funnel);

        if ($lesson->getLessonFile()) {
            $this->uploadCareService->removeFile($lesson->getLessonFile()->getFileUuid());

            $this->entityManager->remove($lesson->getLessonFile());
            $lesson->setLessonFile(null);
            $this->entityManager->persist($funnel);
            $this->entityManager->flush();
        }

        return new RedirectResponse($this->router->generate('hb.sale_funnel.mini_course.edit_lesson', ['id' => $lesson->getId()]));
    }
}