<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\Course;


use HB\AdminBundle\Entity\Customer;
use HB\AdminBundle\Service\SandBoxCourseService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CreateSandBoxController
{
    /**
     * @var SandBoxCourseService
     */
    private $sandBoxCourseService;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var Customer
     */
    private $currentUser;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * CreateSandBoxController constructor.
     *
     * @param SandBoxCourseService  $sandBoxCourseService
     * @param RouterInterface       $router
     * @param TokenStorageInterface $tokenStorage
     * @param FlashBagInterface $flashBag
     */
    public function __construct(SandBoxCourseService $sandBoxCourseService, RouterInterface $router, TokenStorageInterface $tokenStorage, FlashBagInterface $flashBag)
    {
        $this->sandBoxCourseService = $sandBoxCourseService;
        $this->router = $router;
        $this->currentUser = $tokenStorage->getToken()->getUser();
        $this->flashBag = $flashBag;

    }

    /**
     * @return RedirectResponse
     * @throws \HB\AdminBundle\Exception\OnlyAuthorOrProducerCanHaveSandboxException
     */
    public function handleAction()
    {
        if (!$this->sandBoxCourseService->hasSandBox($this->currentUser) && ($this->currentUser->isProducer() || $this->currentUser->isAuthor() || $this->currentUser->isAdmin())) {
            $this->sandBoxCourseService->create($this->currentUser);

            $this->flashBag->add('success', 'Теперь вы можете потренироваться на учебном курсе!');
        } else {
            $this->flashBag->add('error', 'Пользователь не может создать учебный курс');
        }

        return new RedirectResponse($this->router->generate('hb.courses.list'));
    }

}