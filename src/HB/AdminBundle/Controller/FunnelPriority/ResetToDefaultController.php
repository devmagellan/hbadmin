<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\FunnelPriority;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Entity\SaleFunnel\FunnelPriority;
use HB\AdminBundle\Entity\Types\FunnelType;
use HB\AdminBundle\Service\CourseAccessService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

class ResetToDefaultController
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
    private $courseAccessService;

    /**
     * ResetToDefaultController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param RouterInterface        $router
     * @param CourseAccessService    $courseAccessService
     */
    public function __construct(EntityManagerInterface $entityManager, RouterInterface $router, CourseAccessService $courseAccessService)
    {
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->courseAccessService = $courseAccessService;
    }


    /**
     * @param Course $course
     *
     * @return RedirectResponse
     */
    public function handleAction(Course $course)
    {
        $this->courseAccessService->resolveUpdateAccess($course);

        $funnelKeys = FunnelType::possibleTypes();

        $repository = $this->entityManager->getRepository(FunnelPriority::class);

        foreach ($funnelKeys as $key) {
            $existedPriority = $repository->findOneBy(['course' => $course, 'funnelKey' => $key]);
            $defaultPriority = FunnelType::getDefaultPriority($key);

            if ($existedPriority) {
                $existedPriority->setPriority($defaultPriority);
                $this->entityManager->persist($existedPriority);
            } else {
                $newPriority = new FunnelPriority($course, new FunnelType($key), $defaultPriority);
                $this->entityManager->persist($newPriority);
            }
        }

        $this->entityManager->flush();

        return new RedirectResponse($this->router->generate('hb.course.edit', ['id' => $course->getId()]));
    }
}