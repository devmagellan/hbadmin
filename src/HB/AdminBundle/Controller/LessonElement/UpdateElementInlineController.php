<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\LessonElement;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\LessonElement;
use HB\AdminBundle\Service\CourseAccessService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UpdateElementInlineController
{
    private const WEBINAR_AT = 'webinarAt';
    private const CONSULTATION_AT = 'consultationAt';
    private const WEBINAR_TIMEZONE = 'webinarTimezone';
    private const CONSULTATION_TIMEZONE = 'consultationTimezone';

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var CourseAccessService
     */
    private $courseAccessService;

    /**
     * UpdateElementInlineController constructor.
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
        $id = (int) $request->get('pk');
        $field = (string) $request->get('name');
        $value = $request->get('value');

        if (!$value || $value === '') {
            return new JsonResponse('Empty error', 400);
        }

        if (!in_array($field, [self::WEBINAR_AT, self::CONSULTATION_AT, self:: WEBINAR_TIMEZONE, self::CONSULTATION_TIMEZONE])) {
            return new JsonResponse('Invalid field name '.$field, 400);
        }

        if (in_array($field, [self::WEBINAR_AT, self::CONSULTATION_AT])) {
            $value= \DateTime::createFromFormat('d.m.Y H:i', $value);
            $compareDate = new \DateTime('+1 hour');
            if ($value < $compareDate) {
                return new JsonResponse('Minimal date '. $compareDate->format('d.m.Y H:i'), 400);
            }
        }


        /** @var LessonElement $lessonElement */
        $lessonElement = $this->entityManager->getRepository(LessonElement::class)->find($id);

        if (!$lessonElement) {
            return new JsonResponse('Invalid request '.$lessonElement->getId(), 400);
        }

        $this->courseAccessService->resolveUpdateAccess($lessonElement->getLesson()->getSection()->getCourse());

        $method = 'set'.$field;
        $lessonElement->$method($value);

        $this->entityManager->persist($lessonElement);
        $this->entityManager->flush();

        return new JsonResponse('true', 200);
    }
}
