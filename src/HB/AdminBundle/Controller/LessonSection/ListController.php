<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\LessonSection;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Entity\Lesson;
use HB\AdminBundle\Entity\LessonSection;
use HB\AdminBundle\Form\Lesson\LessonMainType;
use HB\AdminBundle\Form\LessonSectionType;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\FormHandler;
use HB\AdminBundle\Service\Intercom\IntercomEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;

class ListController
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var FormHandler
     */
    private $formHandler;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var CourseAccessService
     */
    private $courseAccessService;

    /**
     * ListController constructor.
     *
     * @param \Twig_Environment      $twig
     * @param EntityManagerInterface $entityManager
     * @param FormFactoryInterface   $formFactory
     * @param FormHandler            $formHandler
     * @param FlashBagInterface      $flashBag
     * @param RouterInterface        $router
     * @param CourseAccessService    $courseAccessService
     */
    public function __construct(\Twig_Environment $twig, EntityManagerInterface $entityManager, FormFactoryInterface $formFactory, FormHandler $formHandler, FlashBagInterface $flashBag, RouterInterface $router, CourseAccessService $courseAccessService)
    {
        $this->twig = $twig;
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
        $this->formHandler = $formHandler;
        $this->flashBag = $flashBag;
        $this->router = $router;
        $this->courseAccessService = $courseAccessService;
    }

    /**
     * @param Course  $course
     * @param Request $request
     *
     * @return Response
     */
    public function handleAction(Course $course, Request $request): Response
    {
        $this->courseAccessService->resolveCreateAccess($course);

        $lessonSection = new LessonSection($course);
        $form = $this->formFactory->create(LessonSectionType::class, $lessonSection);

        $lesson = new Lesson();
        $formLesson = $this->formFactory->create(LessonMainType::class, $lesson, [
            'courseId' => $course->getId(),
        ]);

        if (
            $this->formHandler->handle($lessonSection, $request, $form)) {
            $this->flashBag->add('success', 'Раздел добавлен.');

            foreach ($course->getSalesFunnelOrganic()->getPriceBlocks() as $block) {
                $lessonSection->addPriceBlock($block);
            }

            $this->entityManager->persist($lessonSection);
            $this->entityManager->flush();

            return new RedirectResponse($this->router->generate('hb.lesson_section.list', ['id' => $lessonSection->getCourse()->getId()]));
        }

        if ($this->formHandler->handle($lesson, $request, $formLesson)) {
            $this->flashBag->add('success', 'Урок добавлен.');

            $this->updateLessonPriority($lesson);

            $this->courseAccessService->registerEvent(IntercomEvents::CREATE_LESSON, [
                'description' => $lesson->getTitle()
            ]);
            return new RedirectResponse($this->router->generate('hb.lesson.edit', ['id' => $lesson->getId()]));
        }

        $sections = $this->entityManager->getRepository(LessonSection::class)->findBy(['course' => $course], ['priority' => 'ASC']);

        $content = $this->twig->render('@HBAdmin/LessonSection/list.html.twig', [
            'course'                   => $course,
            'sections'                 => $sections,
            'form'                     => $form->createView(),
            'formLesson'               => $formLesson->createView(),
            'canWorkWithOrganicFunnel' => $this->canWorkWithOrganicFunnel($course),
        ]);

        return new Response($content);
    }


    /**
     * @param Course $course
     *
     * @return bool
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function canWorkWithOrganicFunnel(Course $course)
    {
        $res = $this->entityManager->createQueryBuilder()
            ->select('COUNT(lesson)')
            ->from(Lesson::class, 'lesson')
            ->leftJoin('lesson.section', 'section')
            ->leftJoin('section.course', 'course')
            ->where('course.id = :courseId')
            ->setParameters([
                'courseId' => $course->getId(),
            ])
            ->getQuery()
            ->getSingleScalarResult();

        return (bool) $res;
    }

    /**
     * @param Lesson $lesson
     */
    private function updateLessonPriority(Lesson $lesson)
    {
        $lessonsCount = $lesson->getSection()->getLessons()->count();

        $priority = 0;

        if ($lessonsCount > 0) {
            $priority = $lessonsCount - 1;
        }
        $lesson->setPriority( $priority);
        $this->entityManager->persist($lesson);
        $this->entityManager->flush();

    }
}