<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\Course;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\ActionLog;
use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Entity\Customer;
use HB\AdminBundle\Entity\Types\CourseStatusType;
use HB\AdminBundle\Service\EmailSender;
use HB\AdminBundle\Validator\FunnelsExternalLinksValidator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ChangeStatusController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var Customer
     */
    private $currentUser;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var EmailSender
     */
    private $emailSender;

    /**
     * ChangeStatusController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param TokenStorageInterface  $tokenStorage
     * @param FlashBagInterface      $flashBag
     * @param RouterInterface        $router
     * @param EmailSender            $emailSender
     */
    public function __construct(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage, FlashBagInterface $flashBag, RouterInterface $router, EmailSender $emailSender)
    {
        $this->entityManager = $entityManager;
        $this->currentUser = $tokenStorage->getToken()->getUser();
        $this->flashBag = $flashBag;
        $this->router = $router;
        $this->emailSender = $emailSender;
    }

    /**
     * @param Course  $course
     * @param string  $status
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function handleAction(Course $course, string $status, Request $request): RedirectResponse
    {
        $referer = $request->headers->get('referer', $this->router->generate('hb.courses.list'));

        if ($this->currentUser->isAdmin()) {

            if (CourseStatusType::STATUS_PUBLISHED === $status) {
                $errors = FunnelsExternalLinksValidator::validate($course);

                if (!empty($errors)) {

                    foreach ($errors as $error) {
                        $this->flashBag->add('error', $error);
                    }

                    return new RedirectResponse($referer);
                }
            }

            $status = new CourseStatusType($status);
            $course->setStatus($status);

            $this->entityManager->persist($course);
            $this->entityManager->flush();

            if (CourseStatusType::STATUS_PUBLISHED === $status->getValue()) {
                $this->emailSender->coursePublishedEmail($course);
            }

            $this->confirmCourseLogs($course);

            $this->flashBag->add('success', 'Статус изменен');
        } else {
            $this->flashBag->add('error', 'Вам запрещено менять статус');
        }

        return new  RedirectResponse($referer);
    }

    private function confirmCourseLogs(Course $course)
    {
        $this->entityManager->
        createQueryBuilder()
            ->update(ActionLog::class, 'log')
            ->set('log.published', 'true')
            ->where('log.course = :course')
            ->setParameter('course', $course)
            ->getQuery()->getResult();
    }
}