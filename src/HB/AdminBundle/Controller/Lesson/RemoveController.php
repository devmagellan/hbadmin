<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\Lesson;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\Lesson;
use HB\AdminBundle\Service\CourseAccessService;
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
     * @var CourseAccessService
     */
    private $courseAccessService;

    /**
     * RemoveController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param RouterInterface        $router
     * @param FlashBagInterface      $flashBag
     * @param CourseAccessService    $courseAccessService
     */
    public function __construct(EntityManagerInterface $entityManager, RouterInterface $router, FlashBagInterface $flashBag, CourseAccessService $courseAccessService)
    {
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->flashBag = $flashBag;
        $this->courseAccessService = $courseAccessService;
    }


    /**
     * @param Lesson  $lesson
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function handleAction(Lesson $lesson, Request $request): RedirectResponse
    {
        $this->courseAccessService->resolveDeleteAccess($lesson->getSection()->getCourse());

        $referer = $request->headers->get('referer', $this->router->generate('hb.course.edit', ['id' => $lesson->getSection()->getCourse()->getId()]));

        $this->entityManager->remove($lesson);
        $this->entityManager->flush();

        $this->flashBag->add('success', 'Урок удален');

        return new RedirectResponse($referer);
    }

}