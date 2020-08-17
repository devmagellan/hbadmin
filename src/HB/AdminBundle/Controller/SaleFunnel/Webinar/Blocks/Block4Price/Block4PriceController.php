<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\Webinar\Blocks\Block4Price;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelWebinar;
use HB\AdminBundle\Entity\SaleFunnel\Webinar\WebinarPromoCode;
use HB\AdminBundle\Form\SaleFunnel\Webinar\Block4PriceType;
use HB\AdminBundle\Form\SaleFunnel\Webinar\Block4PromoCodeType;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\FormHandler;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Block4PriceController
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
     * Block4PriceController constructor.
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

        $formPrice = $this->formFactory->create(Block4PriceType::class, $funnel);
        $saved = false;

        $promocode = new WebinarPromoCode($this->generateRandomString());
        $formPromoCode = $this->formFactory->create(Block4PromoCodeType::class, $promocode);
        $added = false;

        if ($this->formHandler->handle($funnel, $request, $formPrice)) {
            $saved = true;
        }

        if ($this->formHandler->handle($promocode, $request, $formPromoCode)) {

            $funnel->addPromoCode($promocode);

            $this->entityManager->persist($promocode);
            $this->entityManager->persist($funnel);
            $this->entityManager->flush();
            $added = true;
        }

        $content = $this->twig->render('@HBAdmin/SaleFunnel/Webinar/blocks/block4price.html.twig', [
            'funnel'        => $funnel,
            'formPrice'     => $formPrice->createView(),
            'formPromocode' => $formPromoCode->createView(),
            'saved'         => $saved,
            'added'         => $added,
        ]);

        return new Response($content);
    }

    private function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}