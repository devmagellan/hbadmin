<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\AuthorClub;


use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelAuthorClub;
use HB\AdminBundle\Form\SaleFunnel\AuthorClub\AuthorClubType;
use HB\AdminBundle\Service\Intercom\IntercomEventRecorder;
use HB\AdminBundle\Service\Intercom\IntercomEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;

class InfoController
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var IntercomEventRecorder
     */
    private $eventRecorder;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * InfoController constructor.
     *
     * @param \Twig_Environment     $twig
     * @param IntercomEventRecorder $eventRecorder
     * @param FormFactoryInterface  $formFactory
     */
    public function __construct(\Twig_Environment $twig, IntercomEventRecorder $eventRecorder, FormFactoryInterface $formFactory)
    {
        $this->twig = $twig;
        $this->eventRecorder = $eventRecorder;
        $this->formFactory = $formFactory;
    }

    /**
     * @param Course $course
     *
     * @return Response
     */
    public function handleAction(Course $course): Response
    {
        $this->eventRecorder->registerEvent(IntercomEvents::ACCESS_FUNNEL_CLUB);
        $funnel = $course->getSalesFunnelAuthorClub() ?: new SalesFunnelAuthorClub($course);

        $form = $this->formFactory->create(AuthorClubType::class, $funnel);

        $content = $this->twig->render('@HBAdmin/SaleFunnel/AuthorClub/info.html.twig', [
            'course' => $course,
            'form'   => $form->createView(),
        ]);

        return new Response($content);
    }
}
