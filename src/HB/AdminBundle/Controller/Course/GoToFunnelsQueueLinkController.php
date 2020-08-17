<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\Course;


use HB\AdminBundle\Service\Intercom\IntercomEventRecorder;
use HB\AdminBundle\Service\Intercom\IntercomEvents;
use Symfony\Component\HttpFoundation\RedirectResponse;

class GoToFunnelsQueueLinkController
{
    /**
     * @var IntercomEventRecorder
     */
    private $intercomEventRecorder;

    /**
     * GoToFunnelsQueueLinkController constructor.
     *
     * @param IntercomEventRecorder $intercomEventRecorder
     */
    public function __construct(IntercomEventRecorder $intercomEventRecorder)
    {
        $this->intercomEventRecorder = $intercomEventRecorder;
    }

    /**
     * @return RedirectResponse
     */
    public function handleAction()
    {
        $this->intercomEventRecorder->registerEvent(IntercomEvents::ACCESS_FUNNEL_QUEUE);

        return new RedirectResponse('https://docs.google.com/spreadsheets/d/1TZvQJ0uHp2b7TT1TcwCkDC1supT7PDDq7DiUWtSClZ4/edit#gid=0');
    }
}