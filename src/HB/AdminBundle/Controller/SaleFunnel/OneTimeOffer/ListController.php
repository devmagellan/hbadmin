<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\OneTimeOffer;


use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Service\CourseAccessService;
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
    private $access;

    /**
     * ListController constructor.
     *
     * @param \Twig_Environment   $twig
     * @param CourseAccessService $access
     */
    public function __construct(\Twig_Environment $twig, CourseAccessService $access)
    {
        $this->twig = $twig;
        $this->access = $access;
    }

    /**
     * @param Course  $course
     *
     * @return Response
     */
    public function handleAction(Course $course)
    {
        $this->access->resolveViewAccess($course);

        $content = $this->twig->render('@HBAdmin/SaleFunnel/OneTimeOffer/list.html.twig', [
            'course'            => $course,
        ]);

        return new Response($content);
    }

}