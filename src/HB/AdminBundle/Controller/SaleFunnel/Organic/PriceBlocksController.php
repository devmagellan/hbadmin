<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\Organic;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\CoursePriceBlock;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelOrganic;
use HB\AdminBundle\Form\SaleFunnel\Organic\PriceBlocksType;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\FormHandler;
use HB\AdminBundle\Service\PriceBlockSettingService;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class PriceBlocksController
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var FormFactory
     */
    private $formFactory;

    /**
     * @var FormHandler
     */
    private $formHandler;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * @var CourseAccessService
     */
    private $courseAccess;

    /**
     * @var PriceBlockSettingService
     */
    private $priceBlockSettingService;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * PriceBlocksController constructor.
     *
     * @param \Twig_Environment   $twig
     * @param FormFactory         $formFactory
     * @param FormHandler         $formHandler
     * @param FlashBagInterface   $flashBag
     * @param CourseAccessService $courseAccess
     * @param PriceBlockSettingService $priceBlockSettingService
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(\Twig_Environment $twig, FormFactory $formFactory, FormHandler $formHandler, FlashBagInterface $flashBag, CourseAccessService $courseAccess, PriceBlockSettingService $priceBlockSettingService, EntityManagerInterface $entityManager)
    {
        $this->twig = $twig;
        $this->formFactory = $formFactory;
        $this->formHandler = $formHandler;
        $this->flashBag = $flashBag;
        $this->courseAccess = $courseAccess;
        $this->priceBlockSettingService = $priceBlockSettingService;
        $this->entityManager = $entityManager;
    }


    /**
     * @param SalesFunnelOrganic $funnel
     * @param Request            $request
     *
     * @return Response
     */
    public function handleAction(SalesFunnelOrganic $funnel, Request $request): Response
    {
        $this->courseAccess->resolveCreateAccess($funnel);

        $formPriceBlocks = $this->formFactory->create(PriceBlocksType::class, $funnel);

        if ($this->formHandler->handle($funnel, $request, $formPriceBlocks)) {
            $this->addPriceBlocksOrSaveExisted($funnel);
            $this->flashBag->add('success', 'Тарифные планы сохранены');
        }

        $content = $this->twig->render('@HBAdmin/SaleFunnel/Organic/price_blocks.html.twig', [
            'funnel' => $funnel,
            'form'   => $formPriceBlocks->createView(),
        ]);

        return new Response($content);
    }

    /**
     * Automatically save price block settings
     *
     * @param SalesFunnelOrganic $funnel
     */
    private function addPriceBlocksOrSaveExisted(SalesFunnelOrganic $funnel)
    {
        /** @var CoursePriceBlock $priceBlock */
        foreach ($funnel->getPriceBlocks() as $priceBlock) {
            $setting = $this->priceBlockSettingService->getPriceBlockSetting($funnel, $priceBlock->getType());
            $this->entityManager->persist($setting);
            $this->entityManager->flush();
        }
    }

}