<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Listener;


use HB\AdminBundle\Component\Json;
use HB\AdminBundle\Component\Request\Exception\ViolationListException;
use HB\AdminBundle\Controller\RedirectController;
use HB\AdminBundle\Exception\AccessDeniedForCourseException;
use HB\AdminBundle\Exception\CourseOwnerIsNotDefinedException;
use HB\AdminBundle\Exception\CustomerInteractAccessDeniedException;
use HB\AdminBundle\Service\Intercom\IntercomEventRecorder;
use Sentry\SentryBundle\SentrySymfonyClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class KernelExceptionListener
{
    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var SentrySymfonyClient
     */
    private $sentryErrorHandler;

    /**
     * @var IntercomEventRecorder
     */
    private $intercomEventRecorder;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * KernelExceptionListener constructor.
     *
     * @param FlashBagInterface   $flashBag
     * @param RouterInterface     $router
     * @param SentrySymfonyClient $sentryErrorHandler
     * @param IntercomEventRecorder $intercomEventRecorder
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(FlashBagInterface $flashBag, RouterInterface $router, SentrySymfonyClient $sentryErrorHandler, IntercomEventRecorder $intercomEventRecorder, TokenStorageInterface $tokenStorage)
    {
        $this->flashBag = $flashBag;
        $this->router = $router;
        $this->sentryErrorHandler = $sentryErrorHandler;
        $this->intercomEventRecorder = $intercomEventRecorder;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Handle custom exception
     *
     * @param GetResponseForExceptionEvent $event
     *
     * @return mixed
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        $request = $event->getRequest();
        $url = $request->getBaseUrl() . $request->getUri();

        if ($exception instanceof NotFoundHttpException && strpos($url, RedirectController::SANDBOX_REDIRECT_FLAG)) {
            $this->handleSandboxRedirect($event, $url);
        }

        do {
            if ($exception instanceof AccessDeniedForCourseException || $exception instanceof CourseOwnerIsNotDefinedException || $exception instanceof CustomerInteractAccessDeniedException) {
                $this->handleException($event, $exception);
                $this->intercomEventRecorder->registerAccessExceptionEvent($exception);
                $this->sentryErrorHandler->captureException($exception);
            } else if ($exception instanceof ViolationListException) {
                $this->handleViolationException($event, $exception);
                $this->sentryErrorHandler->captureException($exception);
            } else {
                if ($this->tokenStorage->getToken() && $this->tokenStorage->getToken()->getUser()) {
                    $this->intercomEventRecorder->registerCriticalExceptionEvent($exception);
                }
            }
        } while (null !== $exception = $exception->getPrevious());
    }

    /**
     * @param GetResponseForExceptionEvent $event
     * @param \Exception                   $exception
     */
    private function handleException(GetResponseForExceptionEvent $event, \Exception $exception)
    {
        $request = $event->getRequest();
        $referer = $this->router->generate('hb.courses.list');

        if ($request->isXmlHttpRequest()) {
            $event->setResponse(
                Json::error($exception->getMessage())
            );
        } else {
            $this->flashBag->add('error', $exception->getMessage());
            $event->setResponse(new RedirectResponse($referer));
        }
    }

    /**
     * @param GetResponseForExceptionEvent $event
     * @param \Exception                   $exception
     */
    private function handleViolationException(GetResponseForExceptionEvent $event, \Exception $exception)
    {
        $exceptions = explode('\n', $exception->getMessage());

        $messages = explode(';', $exceptions[0]);
        $messages = array_filter($messages);
        $json = json_encode($messages, JSON_UNESCAPED_UNICODE);

        $response = new JsonResponse(['status' => 'error', 'json' => true, 'message' => $json]);

        $event->setResponse($response);

    }

    /**
     * @param GetResponseForExceptionEvent $event
     * @param string                       $url
     */
    private function handleSandboxRedirect(GetResponseForExceptionEvent $event, string $url)
    {
        $response = new RedirectResponse($this->router->generate('hb.redirect', [
            'url' => urlencode($url)
        ]));

        $event->setResponse($response);
    }
}