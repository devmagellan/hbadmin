<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\LessonSection;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\Lesson;
use HB\AdminBundle\Entity\LessonElement;
use HB\AdminBundle\Entity\LessonSection;
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
     * @param LessonSection $lessonSection
     * @param Request       $request
     *
     * @return RedirectResponse
     */
    public function handleAction(LessonSection $lessonSection, Request $request): RedirectResponse
    {
        $this->courseAccessService->resolveDeleteAccess($lessonSection->getCourse());

        $referer = $request->headers->get('referer', $this->router->generate('hb.lesson_section.list', ['id' => $lessonSection->getCourse()->getId()]));

        /** @var Lesson $lesson */
        foreach ($lessonSection->getLessons() as $lesson) {
            /** @var LessonElement $element */
            foreach ($lesson->getElements() as $element) {
                if (LessonElementType::FILE === $element->getType()->getValue()) {
                    $this->uploadCareService->removeFile($element->getFile()->getFileUuid());
                }
            }
        }

        $this->entityManager->remove($lessonSection);
        $this->entityManager->flush();

        $this->flashBag->add('success', 'Раздел удален');

        return new RedirectResponse($referer);
    }

}