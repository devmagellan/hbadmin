<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\DownSell;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelDownSell;
use HB\AdminBundle\Form\SaleFunnel\DownSell\DownSellType;
use HB\AdminBundle\Service\CourseAccessService;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;

class EditController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var CourseAccessService
     */
    private $access;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * EditController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param RouterInterface        $router
     * @param FormFactoryInterface   $formFactory
     * @param \Twig_Environment      $twig
     * @param CourseAccessService    $access
     * @param FlashBagInterface      $flashBag
     */
    public function __construct(EntityManagerInterface $entityManager, RouterInterface $router, FormFactoryInterface $formFactory, \Twig_Environment $twig, CourseAccessService $access, FlashBagInterface $flashBag)
    {
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->formFactory = $formFactory;
        $this->twig = $twig;
        $this->access = $access;
        $this->flashBag = $flashBag;
    }

    /**
     * @param SalesFunnelDownSell $funnel
     * @param Request             $request
     *
     * @return Response
     */
    public function handleAction(SalesFunnelDownSell $funnel, Request $request)
    {
        $this->access->resolveUpdateAccess($funnel);

        $form = $this->formFactory->create(DownSellType::class, $funnel, [
            'course' => $funnel->getCourse(),
        ]);

        $form->handleRequest($request);
        $hasClone = $this->hasFunnelClone($funnel);

        if (!$hasClone) {
            if ($form->isValid()) {
                $this->entityManager->persist($funnel);
                $this->entityManager->flush();

                return new RedirectResponse($this->router->generate('hb.sale_funnel.down_sell.list', ['id' => $funnel->getCourse()->getId()]));
            }
        } else {
            $this->flashBag->add('error', 'Такое предложение уже существует.');
        }

        $content = $this->twig->render('@HBAdmin/SaleFunnel/DownSell/edit.html.twig', [
            'course' => $funnel->getCourse(),
            'form'   => $form->createView(),
        ]);

        return new Response($content);
    }

    /**
     * @param SalesFunnelDownSell $editedFunnel
     *
     * @return bool
     */
    private function hasFunnelClone(SalesFunnelDownSell $editedFunnel): bool
    {
        $funnels = $editedFunnel->getCourse()->getSalesFunnelDownSells();
        $editedSectionsIds = $this->getSectionsIds($editedFunnel);

        /** @var SalesFunnelDownSell $funnel */
        foreach ($funnels as $funnel) {

            if ($funnel->getName() === $editedFunnel->getName() && $funnel->getId() !== $editedFunnel->getId()) {

                // name is identical, need check sections
                $sectionsIds = $this->getSectionsIds($funnel);

                if ($sectionsIds == $editedSectionsIds) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param SalesFunnelDownSell $funnel
     *
     * @return array
     */
    private function getSectionsIds(SalesFunnelDownSell $funnel)
    {
        $ids = [];

        foreach ($funnel->getSections() as $section) {
            $ids[] = $section->getId();
        }

        sort($ids);

        return $ids;
    }
}