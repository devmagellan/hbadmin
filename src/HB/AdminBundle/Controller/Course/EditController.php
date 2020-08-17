<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\Course;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Entity\Lesson;
use HB\AdminBundle\Form\CourseType;
use HB\AdminBundle\Form\IntegrationsType;
use HB\AdminBundle\Form\TeachableIdType;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\CustomerAccessService;
use HB\AdminBundle\Service\FormHandler;
use HB\AdminBundle\Service\Intercom\IntercomEvents;
use HB\AdminBundle\Service\TeachableCourseMapper;
use HB\AdminBundle\Validator\SaleFunnelOrganicValidator;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;

class EditController
{
    /**
     * @var FormHandler
     */
    private $formHandler;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

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
     * @var CustomerAccessService
     */
    private $customerAccessService;

    /**
     * @var TeachableCourseMapper
     */
    private $teachableMapper;

    /**
     * EditController constructor.
     *
     * @param FormHandler            $formHandler
     * @param \Twig_Environment      $twig
     * @param FormFactoryInterface   $formFactory
     * @param FlashBagInterface      $flashBag
     * @param RouterInterface        $router
     * @param EntityManagerInterface $entityManager
     * @param CourseAccessService    $courseAccessService
     * @param CustomerAccessService  $customerAccessService
     * @param TeachableCourseMapper  $teachableMapper
     */
    public function __construct(FormHandler $formHandler, \Twig_Environment $twig, FormFactoryInterface $formFactory, FlashBagInterface $flashBag, RouterInterface $router, EntityManagerInterface $entityManager, CourseAccessService $courseAccessService, CustomerAccessService $customerAccessService, TeachableCourseMapper $teachableMapper)
    {
        $this->formHandler = $formHandler;
        $this->twig = $twig;
        $this->formFactory = $formFactory;
        $this->flashBag = $flashBag;
        $this->router = $router;
        $this->entityManager = $entityManager;
        $this->courseAccessService = $courseAccessService;
        $this->customerAccessService = $customerAccessService;
        $this->teachableMapper = $teachableMapper;
    }


    /**
     * @param Course  $course
     * @param Request $request
     *
     * @return Response
     */
    public function handleAction(Course $course, Request $request): Response
    {
        $this->courseAccessService->resolveUpdateAccess($course);
        $this->courseAccessService->registerEvent(IntercomEvents::ACCESS_DASHBOARD);

        $formMainInfo = $this->formFactory->create(CourseType::class, $course, [
            'authors' => $this->customerAccessService->getAvailableAuthorsForCourseCreate(),
        ]);

        $formIntegrations = $this->formFactory->create(IntegrationsType::class, $course->getIntegrations(), [
            'owner_packet' => $course->getOwner()->getPacket()->getType(),
        ]);

        $formTeachable = $this->formFactory->create(TeachableIdType::class, $course);

        if ($this->formHandler->handle($course, $request, $formMainInfo)) {
            $this->flashBag->add('success', 'Данные обновлены');
            return new RedirectResponse($this->router->generate('hb.lesson_section.list', ['id' => $course->getId()]));
        }

        if ($this->courseAccessService->getCurrentUser()->isAdmin() && $this->formHandler->handle($course, $request, $formTeachable)) {
            $this->teachableMapper->updateTeachableRelations($course);
            $this->flashBag->add('success', 'Данные обновлены');
            return new RedirectResponse($this->router->generate('hb.course.edit', ['id' => $course->getId()]));
        }

        if ($this->formHandler->handle($course, $request, $formIntegrations)) {
            $this->flashBag->add('success', 'Данные обновлены');
            return new RedirectResponse($this->router->generate('hb.course.edit', ['id' => $course->getId()]));
        }

        $saleFunnelOrganicErrors = $course->isSandbox()
            ? []
            : SaleFunnelOrganicValidator::validate($course->getSalesFunnelOrganic());

        if (\count($saleFunnelOrganicErrors)) {
            $this->flashBag->add('warning', 'Для доступа к остальным воронкам заполните Органическую воронку');
        }

        $content = $this->twig->render('@HBAdmin/Course/edit.html.twig', [
            'formMainInfo'            => $formMainInfo->createView(),
            'formIntegrations'        => $formIntegrations->createView(),
            'formTeachable'           => $formTeachable->createView(),
            'course'                  => $course,
            'showFunnels'             => $this->canWorkWithSaleFunnels($course),
            'saleFunnelOrganicErrors' => (bool) \count($saleFunnelOrganicErrors),
        ]);

        return new Response($content);
    }

    /**
     * @param Course $course
     *
     * @return bool
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function canWorkWithSaleFunnels(Course $course)
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
}
