<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller;


use HB\AdminBundle\Entity\Customer;
use HB\AdminBundle\Service\SandBoxCourseService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class RedirectController
{
    public const SANDBOX_REDIRECT_FLAG = 'sandbox_course';

    /**
     * @var SandBoxCourseService
     */
    private $sandBoxService;

    /**
     * @var Customer
     */
    private $customer;

    /**
     * RedirectController constructor.
     *
     * @param SandBoxCourseService $sandBoxService
     * @param TokenStorageInterface             $tokenStorage
     */
    public function __construct(SandBoxCourseService $sandBoxService, TokenStorageInterface $tokenStorage)
    {
        $this->sandBoxService = $sandBoxService;
        $this->customer = $tokenStorage->getToken()->getUser();
    }

    /**
     * @param string $url
     */
    public function handleAction(string $url)
    {
        $url = urldecode($url);

        if ($this->sandBoxService->hasSandBox($this->customer)) {
            $course = $this->sandBoxService->getCustomerSandboxCourse($this->customer);
        } else {
            $course = $this->sandBoxService->create($this->customer);
        }

        $redirectUrl = str_replace(self::SANDBOX_REDIRECT_FLAG, $course->getId(), $url);

        return new RedirectResponse($redirectUrl);
    }

}