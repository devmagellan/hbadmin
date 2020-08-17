<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\Organic\PriceBlock;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\CoursePriceBlock;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelOrganic;
use HB\AdminBundle\Entity\SaleFunnelOrganicPriceBlockThesis;
use HB\AdminBundle\Form\SaleFunnelOrganicPriceBlockThesisType;
use HB\AdminBundle\Form\PriceBlockSetting\SettingWithCoursesVIPType;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\FormHandler;
use HB\AdminBundle\Service\PriceBlockSettingService;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PriceBlockVIPController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var FormHandler
     */
    private $formHandler;

    /**
     * @var PriceBlockSettingService
     */
    private $priceBlockSettingService;

    /**
     * @var CourseAccessService
     */
    private $access;

    /**
     * PriceBlockVIPController constructor.
     *
     * @param EntityManagerInterface   $entityManager
     * @param FormFactoryInterface     $formFactory
     * @param \Twig_Environment        $twig
     * @param FormHandler              $formHandler
     * @param PriceBlockSettingService $priceBlockSettingService
     * @param CourseAccessService      $access
     */
    public function __construct(EntityManagerInterface $entityManager, FormFactoryInterface $formFactory, \Twig_Environment $twig, FormHandler $formHandler, PriceBlockSettingService $priceBlockSettingService, CourseAccessService $access)
    {
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
        $this->twig = $twig;
        $this->formHandler = $formHandler;
        $this->priceBlockSettingService = $priceBlockSettingService;
        $this->access = $access;
    }


    /**
     * @param SalesFunnelOrganic $funnel
     * @param Request            $request
     *
     * @return Response
     */
    public function handleAction(SalesFunnelOrganic $funnel, Request $request)
    {
        $this->access->resolveCreateAccess($funnel);

        $saleFunnelOrganicPriceBlockSetting = $this->priceBlockSettingService->getPriceBlockSetting($funnel, CoursePriceBlock::TYPE_VIP);
        $form = $this->formFactory->create(SettingWithCoursesVIPType::class, $saleFunnelOrganicPriceBlockSetting, [
            'current_course' => $funnel->getCourse(),
        ]);

        $thesis = new SaleFunnelOrganicPriceBlockThesis('', $saleFunnelOrganicPriceBlockSetting);
        $formThesis = $this->formFactory->create(SaleFunnelOrganicPriceBlockThesisType::class, $thesis);

        $saved = false;
        $added = false;

        if ($this->formHandler->handle($saleFunnelOrganicPriceBlockSetting, $request, $form)) {
            $saved = true;
        }

        // Restrict more than 10 blocks
        if (count($funnel->getPriceBlocks()) < SaleFunnelOrganicPriceBlockThesis::MAX_ADDITIONAL_THESIS) {
            if ($this->formHandler->handle($thesis, $request, $formThesis)) {
                $added = true;
                $thesis = new SaleFunnelOrganicPriceBlockThesis('', $saleFunnelOrganicPriceBlockSetting);
                $formThesis = $this->formFactory->create(SaleFunnelOrganicPriceBlockThesisType::class, $thesis);
            }
        }

        $content = $this->twig->render('@HBAdmin/SaleFunnel/Organic/PriceBlock/vip.html.twig', [
            'saved'                              => $saved,
            'added'                              => $added,
            'course'                             => $funnel,
            'form'                               => $form->createView(),
            'formThesis'                         => $formThesis->createView(),
            'saleFunnelOrganicPriceBlockSetting' => $saleFunnelOrganicPriceBlockSetting,
            'lessonCount'                        => $this->priceBlockSettingService->getLessonCount($funnel->getCourse(), CoursePriceBlock::TYPE_VIP),
            'funnel'                             => $funnel,
        ]);

        return new Response($content);
    }
}