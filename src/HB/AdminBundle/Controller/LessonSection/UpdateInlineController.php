<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\LessonSection;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\LessonSection;
use HB\AdminBundle\Service\CourseAccessService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UpdateInlineController
{
    private const FIELD_TITLE = 'title';

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var CourseAccessService
     */
    private $courseAccessService;

    /**
     * UpdateInlineController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param CourseAccessService    $courseAccessService
     */
    public function __construct(EntityManagerInterface $entityManager, CourseAccessService $courseAccessService)
    {
        $this->entityManager = $entityManager;
        $this->courseAccessService = $courseAccessService;
    }


    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function handleAction(Request $request): JsonResponse
    {
        $id = $request->get('pk');
        $field = $request->get('name');
        $value = $request->get('value');

        if (!$value || $value === '') {
            return new JsonResponse('Empty error', 400);
        }

        if (mb_strlen($value) > 200) {
            return new JsonResponse('Max length 200 symbols. You have '.mb_strlen($value), 400);
        }

        if (!in_array($field, [self::FIELD_TITLE])) {
            return new JsonResponse('Invalid field name '.$field, 400);
        }

        if (self::FIELD_TITLE === $field) {
            $section = $this->entityManager->getRepository(LessonSection::class)->find($id);
            $this->courseAccessService->resolveUpdateAccess($section->getCourse());

            if (!$section) {
                return new JsonResponse('Invalid request'.$section->getId(), 400);
            }

            $section->setTitle($value);
            $this->entityManager->persist($section);
            $this->entityManager->flush();

            return new JsonResponse('true', 200);
        }

        return new JsonResponse("System Error ", 400);
    }
}
