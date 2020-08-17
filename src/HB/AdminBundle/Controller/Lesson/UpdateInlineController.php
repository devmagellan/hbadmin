<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\Lesson;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\Lesson;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\DecodeHelper;
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
    public function handleAction(Request $request)
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
            /** @var Lesson $lesson */
            $lesson = $this->entityManager->getRepository(Lesson::class)->find($id);

            if (!$lesson) {
                return new JsonResponse('Invalid request', 400);
            }
            $this->courseAccessService->resolveUpdateAccess($lesson->getSection()->getCourse());

            $lesson->setTitle($value);
            $this->entityManager->persist($lesson);
            $this->entityManager->flush();

            return new JsonResponse('true', 200);
        }

        return new JsonResponse("System Error ", 400);
    }
}
