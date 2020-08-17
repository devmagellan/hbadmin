<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\AuthorClub;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelAuthorClub;
use HB\AdminBundle\Form\SaleFunnel\AuthorClub\AuthorClubType;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\FormHandler;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

class UpdateController
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
    private $accessService;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var FormHandler
     */
    private $formHandler;

    /**
     * UpdateController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param RouterInterface        $router
     * @param CourseAccessService    $accessService
     * @param FormFactoryInterface   $formFactory
     * @param FormHandler            $formHandler
     */
    public function __construct(EntityManagerInterface $entityManager, RouterInterface $router, CourseAccessService $accessService, FormFactoryInterface $formFactory, FormHandler $formHandler)
    {
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->accessService = $accessService;
        $this->formFactory = $formFactory;
        $this->formHandler = $formHandler;
    }

    /**
     * @param Course  $course
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function handleAction(Course $course, Request $request)
    {
        $takePart = (bool) $request->get('take_part');

        $funnel = $course->getSalesFunnelAuthorClub()
            ?: new SalesFunnelAuthorClub($course);

        $form = $this->formFactory->create(AuthorClubType::class, $funnel);

        if ($takePart) {
            $this->accessService->resolveCreateAccess($funnel);

            $course->setSalesFunnelAuthorClub($funnel);
            $this->entityManager->persist($funnel);

            $this->formHandler->handle($funnel, $request, $form);
        } else {
            $this->accessService->resolveDeleteAccess($course);

            $course->setSalesFunnelAuthorClub(null);

            $this->entityManager->persist($course);
            $this->entityManager->remove($funnel);
        }
        $this->entityManager->flush();

        return new RedirectResponse($this->router->generate('hb.course.edit', ['id'   => $course->getId()]));
    }
}
