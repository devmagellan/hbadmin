<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\Webinar\Blocks\Block6Offer;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Component\Json;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelWebinar;
use HB\AdminBundle\Entity\SaleFunnel\Webinar\WebinarOffer;
use HB\AdminBundle\Form\SaleFunnel\Webinar\Block6DownSellOfferBlockType;
use HB\AdminBundle\Form\SaleFunnel\Webinar\Block6OfferType;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\FormHandler;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Block6OfferController
{
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
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var CourseAccessService
     */
    private $courseAccessService;

    /**
     * Block6OfferController constructor.
     *
     * @param FormFactoryInterface   $formFactory
     * @param \Twig_Environment      $twig
     * @param FormHandler            $formHandler
     * @param EntityManagerInterface $entityManager
     * @param CourseAccessService    $courseAccessService
     */
    public function __construct(FormFactoryInterface $formFactory, \Twig_Environment $twig, FormHandler $formHandler, EntityManagerInterface $entityManager, CourseAccessService $courseAccessService)
    {
        $this->formFactory = $formFactory;
        $this->twig = $twig;
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

        $offersCount = \count($funnel->getOffers());

        if ($request->getMethod() === Request::METHOD_POST && $offersCount >= 3) {
            return Json::error('Превышено количество предложений');
        }

        $offerDescriptionForm = $this->formFactory->create(Block6OfferType::class, $funnel);

        $saved = false;
        $added = false;
        $offerExistStatus = false;


        if ($funnel->getCourse()->getSalesFunnelOrganic()) {
            $priceBlocks = $funnel->getCourse()->getSalesFunnelOrganic()->getPriceBlocks();
        } else {
            $priceBlocks = [];
        }

        if ($this->formHandler->handle($funnel, $request, $offerDescriptionForm)) {
            $saved = true;
        }

        $offer = new WebinarOffer();
        $offerForm = $this->formFactory->create(Block6DownSellOfferBlockType::class, $offer, [
            'price_blocks' => $priceBlocks,
        ]);


        if ($this->formHandler->handle($offer, $request, $offerForm)) {

            $funnelOfferExist = false;

            /** @var WebinarOffer $offerExist */
            foreach ($funnel->getOffers() as $offerExist) {
                if ($offerExist->getTitle() === $offer->getTitle()) {
                    $funnelOfferExist = true;
                    $funnel->removeOffer($offer);
                    $this->entityManager->remove($offer);
                    $offerExistStatus = true;
                    break;
                }
            }
            if (!$funnelOfferExist) {
                $funnel->addOffer($offer);
                $added = true;
            } else {
                $added = false;
            }

            $this->entityManager->persist($funnel);
            $this->entityManager->flush();
        }

        $content = $this->twig->render('@HBAdmin/SaleFunnel/Webinar/blocks/block6offer.html.twig', [
            'formOffer'            => $offerForm->createView(),
            'formOfferDescription' => $offerDescriptionForm->createView(),
            'saved'                => $saved,
            'added'                => $added,
            'funnel'               => $funnel,
            'offerExistStatus'     => $offerExistStatus,
        ]);

        return new Response($content);
    }
}