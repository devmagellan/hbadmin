<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\Webinar\Blocks\Block1TargetPage;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelWebinar;
use HB\AdminBundle\Entity\SaleFunnel\Webinar\WebinarDate;
use HB\AdminBundle\Entity\SaleFunnel\Webinar\WebinarThesis;
use HB\AdminBundle\Form\SaleFunnel\Webinar\TargetPageType;
use HB\AdminBundle\Form\SaleFunnel\Webinar\WebinarThesisType;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\FormHandler;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Block1TargetPageController
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var FormHandler
     */
    private $formHandler;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var CourseAccessService
     */
    private $courseAccessService;

    /**
     * Block1TargetPageController constructor.
     *
     * @param \Twig_Environment      $twig
     * @param FormFactoryInterface   $formFactory
     * @param FormHandler            $formHandler
     * @param EntityManagerInterface $entityManager
     * @param CourseAccessService    $courseAccessService
     */
    public function __construct(\Twig_Environment $twig, FormFactoryInterface $formFactory, FormHandler $formHandler, EntityManagerInterface $entityManager, CourseAccessService $courseAccessService)
    {
        $this->twig = $twig;
        $this->formFactory = $formFactory;
        $this->formHandler = $formHandler;
        $this->entityManager = $entityManager;
        $this->courseAccessService = $courseAccessService;
    }


    /**
     * @param SalesFunnelWebinar $funnel
     * @param Request            $request
     *
     * @return Response
     */
    public function handleAction(SalesFunnelWebinar $funnel, Request $request)
    {
        $this->courseAccessService->resolveCreateAccess($funnel);

        $form = $this->formFactory->create(TargetPageType::class, $funnel);

        $thesis = new WebinarThesis();
        $formThesis = $this->formFactory->create(WebinarThesisType::class, $thesis);

        $saved = false;

        if ($this->formHandler->handle($funnel, $request, $form)) {

            /** @var SalesFunnelWebinar $data */
            $data = $form->getData();

            /** @var WebinarDate $webinarDate */
            foreach ($data->getDates() as $webinarDate) {

                $webinarDate->setWebinar($funnel);
                $this->entityManager->persist($funnel);
                $this->entityManager->persist($webinarDate);
            }

            $this->entityManager->flush();

            $saved = true;
        }

        if ($this->formHandler->handle($thesis, $request, $formThesis)) {
            $funnel->addThesis($thesis);
            $this->entityManager->persist($funnel);
            $this->entityManager->flush();

            $thesis = new WebinarThesis();
            $formThesis = $this->formFactory->create(WebinarThesisType::class, $thesis);
        }

        $content = $this->twig->render('@HBAdmin/SaleFunnel/Webinar/blocks/block1target_page.html.twig', [
            'form'       => $form->createView(),
            'formThesis' => $formThesis->createView(),
            'saved'      => $saved,
            'funnel'     => $funnel,
        ]);

        return new Response($content);
    }

}