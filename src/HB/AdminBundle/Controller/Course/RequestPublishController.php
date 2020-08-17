<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\Course;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Entity\Types\CourseStatusType;
use HB\AdminBundle\Service\Intercom\IntercomEventRecorder;
use HB\AdminBundle\Service\Intercom\IntercomEvents;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;

class RequestPublishController
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
     * @var IntercomEventRecorder
     */
    private $eventRecorder;

    /**
     * RequestModerateController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param RouterInterface        $router
     * @param FlashBagInterface      $flashBag
     * @param IntercomEventRecorder  $eventRecorder
     */
    public function __construct(EntityManagerInterface $entityManager, RouterInterface $router, FlashBagInterface $flashBag, IntercomEventRecorder $eventRecorder)
    {
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->flashBag = $flashBag;
        $this->eventRecorder = $eventRecorder;
    }

    /**
     * @param Course  $course
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function handleAction(Course $course, Request $request): RedirectResponse
    {
        $referer = $request->headers->get('referer');

        if (CourseStatusType::STATUS_IN_PROGRESS === $course->getStatus()->getValue() || CourseStatusType::STATUS_PUBLISHED === $course->getStatus()->getValue()) {

            $course->setStatus(new CourseStatusType(CourseStatusType::STATUS_WAIT_PUBLISH));
            $course->setHadPublishRequest(true);
            $this->entityManager->persist($course);
            $this->entityManager->flush();

            $this->flashBag->add('success', 'При первой публикации вашего продукта на нашей платформе мы вручную проверим его соответствие правилам использования платформы,  убедимся что все видео четкие, а звук ясный. Как только ваш продукт будет опубликован, его статус в личном кабинете поменяется на опубликован, а вы получите уведомление на электронную почту и сразу же сможете его продавать. Публикация всех последующих изменений осуществляется автоматически.');
            $this->eventRecorder->registerEvent(IntercomEvents::SEND_PRODUCT_TO_MODERATION);
        } else {
            $this->flashBag->add('error', 'Невозможно опубликовать');
        }

        return new RedirectResponse($referer);
    }
}