<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\Webinar;


use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\Intercom\IntercomEvents;
use Symfony\Component\HttpFoundation\Response;

class ListController
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var CourseAccessService
     */
    private $courseAccessService;

    /**
     * ListController constructor.
     *
     * @param \Twig_Environment   $twig
     * @param CourseAccessService $courseAccessService
     */
    public function __construct(\Twig_Environment $twig, CourseAccessService $courseAccessService)
    {
        $this->twig = $twig;
        $this->courseAccessService = $courseAccessService;
    }


    /**
     * @param Course $course
     *
     * @return Response
     */
    public function handleAction(Course $course)
    {
        $this->courseAccessService->resolveViewAccess($course);
        $this->courseAccessService->registerEvent(IntercomEvents::ACCESS_FUNNEL_WEBINAR);

        $content = $this->twig->render('@HBAdmin/SaleFunnel/Webinar/list.html.twig', [
            'course' => $course
        ]);

        return new Response($content);
    }
}