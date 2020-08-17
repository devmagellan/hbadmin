<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\Organic;


use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\Intercom\IntercomEvents;
use HB\AdminBundle\Validator\SaleFunnelOrganicValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class EditController
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var CourseAccessService
     */
    private $courseAccess;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * EditController constructor.
     *
     * @param \Twig_Environment   $twig
     * @param CourseAccessService $courseAccess
     * @param FlashBagInterface $flashBag
     */
    public function __construct(\Twig_Environment $twig, CourseAccessService $courseAccess, FlashBagInterface $flashBag)
    {
        $this->twig = $twig;
        $this->courseAccess = $courseAccess;
        $this->flashBag = $flashBag;
    }

    /**
     * @param Course  $course
     * @param Request $request
     *
     * @return Response
     */
    public function handleAction(Course $course): Response
    {
        $funnel = $course->getSalesFunnelOrganic();

        $this->courseAccess->resolveViewAccess($funnel);
        $this->courseAccess->registerEvent(IntercomEvents::ACCESS_FUNNEL_ORGANIC);

        $saleFunnelOrganicErrors = $course->isSandbox()
            ? []
            : SaleFunnelOrganicValidator::validate($course->getSalesFunnelOrganic());

        if (\count($saleFunnelOrganicErrors)) {
            foreach ($saleFunnelOrganicErrors as $error) {
                $this->flashBag->add('error', $error);
            }
        }

        $content = $this->twig->render('@HBAdmin/SaleFunnel/Organic/edit.html.twig', [
            'funnel' => $funnel,
        ]);

        return new Response($content);
    }

}