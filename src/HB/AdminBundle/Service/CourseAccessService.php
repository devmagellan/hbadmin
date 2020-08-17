<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Service;


use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Entity\Customer;
use HB\AdminBundle\Entity\CustomerRole;
use HB\AdminBundle\Entity\SaleFunnel\CourseAccessInterface;
use HB\AdminBundle\Entity\UploadCareFile;
use HB\AdminBundle\Exception\AccessDeniedForCourseException;
use HB\AdminBundle\Exception\CourseOwnerIsNotDefinedException;
use HB\AdminBundle\Service\Intercom\IntercomEventRecorder;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CourseAccessService
{
    /**
     * @var Customer
     */
    private $currentUser;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var Course
     */
    private $course = null;

    /**
     * @var CourseAccessInterface
     */
    private $funnel = null;

    /**
     * @var IntercomEventRecorder
     */
    private $intercomEventRecorder;

    /**
     * CourseAccessService constructor.
     *
     * @param TokenStorageInterface $tokenStorage
     * @param IntercomEventRecorder $eventRecorder
     */
    public function __construct(TokenStorageInterface $tokenStorage, IntercomEventRecorder $eventRecorder)
    {
        $this->tokenStorage = $tokenStorage;
        $this->intercomEventRecorder = $eventRecorder;
    }

    /**
     * Resolve access for some update with course relation
     *
     * @param CourseAccessInterface $funnel
     *
     * @return bool
     * @throws AccessDeniedForCourseException
     * @throws CourseOwnerIsNotDefinedException
     */
    public function resolveUpdateAccess(CourseAccessInterface $funnel)
    {
        $this->init($funnel);

        $courseOwner = $this->getCourseOwner($funnel);

        if ($this->currentUser->isAdmin()) {
            return true;
        }
        else if ($this->currentUser->isProducer() && $courseOwner->getProducer() && $courseOwner->getProducer()->getId() === $this->currentUser->getId()
            || $courseOwner->getId() === $this->currentUser->getId()
        ) {
            return true;
        } else if ($this->currentUser->isProducer() && $this->currentUser->getId() === $courseOwner->getId()) {
            return true;
        } else if ($this->currentUser->isAuthor() && $courseOwner->getId() === $this->currentUser->getId()) {
            return true;
        } else if ($this->currentUser->isManager() && $courseOwner->hasRole(CustomerRole::ROLE_AUTHOR) && $this->currentUser->hasAuthor($courseOwner)) {
            return true;
        }

        throw new AccessDeniedForCourseException(sprintf('Доступ запрещен. Курс: %s. Пользователь: %s. Действие: Update.',
            $funnel->getCourse()->getId(),
            $this->currentUser->getId()
        ));
    }


    /**
     * Resolve access for create something with course relation
     *
     * @param CourseAccessInterface $funnel
     *
     * @return bool
     * @throws AccessDeniedForCourseException
     * @throws CourseOwnerIsNotDefinedException
     */
    public function resolveCreateAccess(CourseAccessInterface $funnel)
    {
        $this->init($funnel);

        $courseOwner = $this->getCourseOwner($funnel);

        if ($this->currentUser->isAdmin()) {
            return true;
        } else if ($this->currentUser->isProducer() && $courseOwner->getProducer() && $courseOwner->getProducer()->getId() === $this->currentUser->getId()
            || $courseOwner->getId() === $this->currentUser->getId()
        ) {
            return true;
        } else if ($this->currentUser->isProducer() && $this->currentUser->getId() === $courseOwner->getId()) {
            return true;
        } else if ($this->currentUser->isAuthor() && $courseOwner->getId() === $this->currentUser->getId()) {
            return true;
        } else if ($this->currentUser->isManager() && $courseOwner->hasRole(CustomerRole::ROLE_AUTHOR) && $this->currentUser->hasAuthor($courseOwner)) {
            return true;
        }

        throw new AccessDeniedForCourseException(sprintf('Доступ запрещен. Курс: %s. Пользователь: %s. Действие: Create.',
            $funnel->getCourse()->getId(),
            $this->currentUser->getId()
        ));
    }


    /**
     * Resolve delete action with something in course / course relation
     *
     * @param CourseAccessInterface $funnel
     *
     * @return bool
     * @throws AccessDeniedForCourseException
     * @throws CourseOwnerIsNotDefinedException
     */
    public function resolveDeleteAccess(CourseAccessInterface $funnel)
    {
        $this->init($funnel);

        $courseOwner = $this->getCourseOwner($funnel);

        if ($this->currentUser->isAdmin()) {
            return true;
        } else if ($this->currentUser->isProducer() && $courseOwner->getProducer() && $courseOwner->getProducer()->getId() === $this->currentUser->getId()
            || $courseOwner->getId() === $this->currentUser->getId()
        ) {
            return true;
        } else if ($this->currentUser->isProducer() && $this->currentUser->getId() === $courseOwner->getId()) {
            return true;
        } else if ($this->currentUser->isAuthor() && $courseOwner->getId() === $this->currentUser->getId()) {
            return true;
        }

        throw new AccessDeniedForCourseException(sprintf('Доступ запрещен. Курс: %s. Пользователь: %s. Действие: Delete.',
            $funnel->getCourse()->getId(),
            $this->currentUser->getId()
        ));
    }

    /**
     * Resolve view access for course / course relation
     *
     * @param CourseAccessInterface $funnel
     *
     * @return bool
     * @throws AccessDeniedForCourseException
     * @throws CourseOwnerIsNotDefinedException
     */
    public function resolveViewAccess(CourseAccessInterface $funnel)
    {
        $this->init($funnel);

        $courseOwner = $this->getCourseOwner($funnel);

        if ($this->currentUser->isAdmin()) {
            return true;
        } else if ($this->currentUser->isProducer() && $courseOwner->getProducer() && $courseOwner->getProducer()->getId() === $this->currentUser->getId()) {
            return true;
        } else if ($this->currentUser->isProducer() && $this->currentUser->getId() === $courseOwner->getId()) {
            return true;
        } else if ($this->currentUser->isAuthor() && $courseOwner->getId() === $this->currentUser->getId()) {
            return true;
        } else if ($this->currentUser->isManager() && $courseOwner->hasRole(CustomerRole::ROLE_AUTHOR) && $this->currentUser->hasAuthor($courseOwner)) {
            return true;
        }

        throw new AccessDeniedForCourseException(sprintf('Доступ запрещен. Курс: %s. Текущеий пользователь: %s. Действие: Просмотр.',
            $funnel->getCourse()->getId(),
            $this->currentUser->getId()
        ));
    }

    /**
     * @return Course|null
     */
    public function getCourse(): ?Course
    {
        return $this->course;
    }

    /**
     * @return CourseAccessInterface|null
     */
    public function getFunnel(): ?CourseAccessInterface
    {
        return $this->funnel;
    }

    /**
     * @return Customer
     */
    public function getCurrentUser(): Customer
    {
        return $this->currentUser;
    }

    /**
     * @param string $event
     * @param array  $metadata
     */
    public function registerEvent(string $event, array $metadata = [])
    {
        $this->intercomEventRecorder->registerEvent($event, $metadata);
    }

    /**
     * @param UploadCareFile $file
     * @param array          $metadata
     */
    public function registerFileAddEvent(UploadCareFile $file, array $metadata = [])
    {
        $this->intercomEventRecorder->registerFileAddEvent($file, $metadata);
    }

    /**
     * @param UploadCareFile $file
     * @param array          $metadata
     */
    public function registerImageAddEvent(UploadCareFile $file, array $metadata = [])
    {
        $this->intercomEventRecorder->registerImageAddEvent($file, $metadata);
    }

    /**
     * @param CourseAccessInterface $courseAccess
     *
     * @return Customer|null
     */
    private function getCourseOwner(CourseAccessInterface $courseAccess): Customer
    {
        $owner = $courseAccess->getCourse()->getOwner();
        if (!$owner) {
            throw new CourseOwnerIsNotDefinedException(sprintf('Создатель курса (id: %s) не идентифицирован.', $courseAccess->getCourse()->getId()));
        }

        return $owner;
    }

    private function init(CourseAccessInterface $funnel)
    {
        if ($funnel instanceof Course) {
            $this->course = $funnel;
        } else {
            $this->course = $funnel->getCourse();
            $this->funnel = $funnel;
        }

        if ($this->tokenStorage->getToken()) {
            $this->currentUser = $this->tokenStorage->getToken()->getUser();
        }
    }

}