<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\LessonElement;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\LessonElement;
use HB\AdminBundle\Entity\Types\LessonElementType;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\UploadCareService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;

class RemoveController
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
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * @var UploadCareService
     */
    private $uploadCareService;

    /**
     * @var CourseAccessService
     */
    private $courseAccessService;

    /**
     * RemoveController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param RouterInterface        $router
     * @param FlashBagInterface      $flashBag
     * @param UploadCareService      $uploadCareService
     * @param CourseAccessService    $courseAccessService
     */
    public function __construct(EntityManagerInterface $entityManager, RouterInterface $router, FlashBagInterface $flashBag, UploadCareService $uploadCareService, CourseAccessService $courseAccessService)
    {
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->flashBag = $flashBag;
        $this->uploadCareService = $uploadCareService;
        $this->courseAccessService = $courseAccessService;
    }

    /**
     * @param LessonElement $lessonElement
     * @param Request       $request
     *
     * @return RedirectResponse
     */
    public function handleAction(LessonElement $lessonElement, Request $request): RedirectResponse
    {
        $course = $lessonElement->getLesson()->getSection()->getCourse();
        $this->courseAccessService->resolveDeleteAccess($course);

        $referer = $request->headers->get('referer', $this->router->generate('hb.lesson.edit', ['id' => $lessonElement->getLesson()->getSection()->getCourse()->getId()]));

        if (LessonElementType::FILE === $lessonElement->getType()->getValue()) {
            if ($lessonElement->getFile()) {
                $this->uploadCareService->removeFile($lessonElement->getFile()->getFileUuid());
            }
        }

        $this->entityManager->remove($lessonElement);
        $this->entityManager->flush();

        $this->flashBag->add('success', 'Элемент удален');

        return new RedirectResponse($referer);
    }

}