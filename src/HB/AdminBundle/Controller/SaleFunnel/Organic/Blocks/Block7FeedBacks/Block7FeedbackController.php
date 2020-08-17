<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\Organic\Blocks\Block7FeedBacks;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\SaleFunnel\Organic\FeedBackVideo;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelOrganic;
use HB\AdminBundle\Entity\UploadCareFile;
use HB\AdminBundle\Form\SaleFunnel\Organic\Block7FeedbackType;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\FormHandler;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Block7FeedbackController
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var FormHandler
     */
    private $formHandler;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var CourseAccessService
     */
    private $courseAccessService;

    /**
     * Block7FeedbackController constructor.
     *
     * @param \Twig_Environment      $twig
     * @param EntityManagerInterface $entityManager
     * @param FormHandler            $formHandler
     * @param FormFactoryInterface   $formFactory
     * @param CourseAccessService    $courseAccessService
     */
    public function __construct(\Twig_Environment $twig, EntityManagerInterface $entityManager, FormHandler $formHandler, FormFactoryInterface $formFactory, CourseAccessService $courseAccessService)
    {
        $this->twig = $twig;
        $this->entityManager = $entityManager;
        $this->formHandler = $formHandler;
        $this->formFactory = $formFactory;
        $this->courseAccessService = $courseAccessService;
    }


    /**
     * @param SalesFunnelOrganic $funnel
     * @param Request            $request
     *
     * @return Response
     */
    public function handleAction(SalesFunnelOrganic $funnel, Request $request)
    {
        $this->courseAccessService->resolveCreateAccess($funnel);

        $feedback = new FeedBackVideo();
        $added = false;

        $form = $this->formFactory->create(Block7FeedbackType::class, $feedback);

        if ($this->formHandler->handle($feedback, $request, $form)) {
            $funnel->addBlock7Feedback($feedback);
            $this->entityManager->persist($feedback);
            $this->entityManager->flush();
            $added = true;

            $fileCdn = $request->get('hb_adminbundle_sale_funnel_organic_block7_feedback')['fileCdn'];
            $fileName = $request->get('hb_adminbundle_sale_funnel_organic_block7_feedback')['fileName'];
            $fileUuid = $request->get('hb_adminbundle_sale_funnel_organic_block7_feedback')['fileUuid'];

            if ($fileName && $fileCdn && $fileUuid) {
                $file = new UploadCareFile($fileUuid, $fileCdn, $fileName);
                $feedback->setFeedBackVideo($file);
                $this->entityManager->persist($file);
                $this->entityManager->persist($feedback);
                $this->entityManager->flush();
                $this->courseAccessService->registerFileAddEvent($file, [
                    'description' => 'Органическая воронка, блок 7, фидбек',
                    'course'      => $funnel->getCourse()->getId(),
                ]);
            }

            $feedback = new FeedBackVideo();
            $form = $this->formFactory->create(Block7FeedbackType::class, $feedback);
        }

        $content = $this->twig->render('@HBAdmin/SaleFunnel/Organic/blocks/block7.html.twig', [
            'form'   => $form->createView(),
            'added'  => $added,
            'funnel' => $funnel,
        ]);

        return new Response($content);
    }
}