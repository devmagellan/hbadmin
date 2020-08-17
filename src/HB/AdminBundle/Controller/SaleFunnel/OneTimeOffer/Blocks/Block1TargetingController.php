<?php

declare(strict_types = 1);

namespace HB\AdminBundle\Controller\SaleFunnel\OneTimeOffer\Blocks;

use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelOneTimeOffer;
use HB\AdminBundle\Form\SaleFunnel\OneTimeOffer\OneTimeOfferType;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\Intercom\IntercomEvents;
use HB\AdminBundle\Service\SaleFunnel\OneTimeOfferService;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;

class Block1TargetingController
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
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var OneTimeOfferService
     */
    private $oneTimeOfferService;

    /**
     * @var CourseAccessService
     */
    private $access;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * Block1TargetingController constructor.
     *
     * @param \Twig_Environment      $twig
     * @param EntityManagerInterface $entityManager
     * @param FormFactoryInterface   $formFactory
     * @param OneTimeOfferService    $oneTimeOfferService
     * @param CourseAccessService    $access
     * @param FlashBagInterface      $flashBag
     * @param RouterInterface        $router
     */
    public function __construct(\Twig_Environment $twig, EntityManagerInterface $entityManager, FormFactoryInterface $formFactory, OneTimeOfferService $oneTimeOfferService, CourseAccessService $access, FlashBagInterface $flashBag, RouterInterface $router)
    {
        $this->twig = $twig;
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
        $this->oneTimeOfferService = $oneTimeOfferService;
        $this->access = $access;
        $this->flashBag = $flashBag;
        $this->router = $router;
    }

    /**
     * @param SalesFunnelOneTimeOffer $funnel
     * @param Request                 $request
     *
     * @return Response
     */
    public function handleAction(SalesFunnelOneTimeOffer $funnel, Request $request)
    {
        $webinars = $this->oneTimeOfferService->getAvailableAuthorWebinars($funnel->getCourse()->getOwner());

        $this->access->resolveCreateAccess($funnel);
        $this->access->registerEvent(IntercomEvents::ACCESS_FUNNEL_OTO);

        $form = $this->formFactory->create(OneTimeOfferType::class, $funnel, [
            'method'   => 'POST',
            'action'   => $this->router->generate('hb.sale_funnel.one_time_offer.blocks.block1_targeting', ['id' => $funnel->getId()]),
            'two_step' => (bool) \count($webinars),
        ]);

        $purposesInUse = $this->oneTimeOfferService->resolvePurposeUse($funnel->getCourse());
        $purposeViolation = $this->usePurposeViolation($purposesInUse);

        if ($request->isMethod(Request::METHOD_POST)) {

            if ($purposeViolation) {
                $this->flashBag->add('error', 'Добавление невозможно, нет свободных страниц для предложения.');

                return new RedirectResponse($request->headers->get('referer'));
            }

            $form->handleRequest($request);

            if ((SalesFunnelOneTimeOffer::ONE_STEP_LEVEL === $funnel->getLevel() && !$funnel->isForEducational() && !$funnel->isForLeadMagnet() && !$funnel->isForMiniCourse() && !$funnel->isForWebinar())
                || (SalesFunnelOneTimeOffer::TWO_STEP_LEVEL === $funnel->getLevel() && !$funnel->isForEducational() && !$funnel->isForLeadMagnet() && !$funnel->isForMiniCourse())
            ) {
                $this->flashBag->add('error', 'Необходимо указать минимум одну связанную воронку');
            } else {
                if ($form->isValid()) {
                    $this->entityManager->persist($funnel);
                    $this->entityManager->flush();
                }
            }
        }

        $content = $this->twig->render('@HBAdmin/SaleFunnel/OneTimeOffer/blocks/block1targeting.html.twig', [
            'funnel'            => $funnel,
            'form'              => $form->createView(),
            'purpose_in_use'    => $purposesInUse,
            'purpose_violation' => $purposeViolation,
            'webinars'          => $webinars,
        ]);

        return new Response($content);
    }

    /**
     * Check if all of OTO purpose is in use
     *
     * @param array $purposes
     *
     * @return bool
     */
    private function usePurposeViolation(array $purposes): bool
    {
        foreach ($purposes as $purpose) {
            if (!$purpose) {
                return false;
            }
        }

        return true;
    }
}