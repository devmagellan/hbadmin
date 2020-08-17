<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\DownSell;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelDownSell;
use HB\AdminBundle\Form\SaleFunnel\DownSell\DownSellType;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\Intercom\IntercomEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class ListController
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
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var CourseAccessService
     */
    private $access;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * ListController constructor.
     *
     * @param \Twig_Environment      $twig
     * @param FormFactoryInterface   $formFactory
     * @param EntityManagerInterface $entityManager
     * @param CourseAccessService    $access
     * @param FlashBagInterface      $flashBag
     */
    public function __construct(\Twig_Environment $twig, FormFactoryInterface $formFactory, EntityManagerInterface $entityManager, CourseAccessService $access, FlashBagInterface $flashBag)
    {
        $this->twig = $twig;
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
        $this->access = $access;
        $this->flashBag = $flashBag;
    }

    /**
     * @param Course  $course
     * @param Request $request
     *
     * @return Response
     */
    public function handleAction(Course $course, Request $request): Response
    {
        $downSell = new SalesFunnelDownSell($course);

        $this->access->resolveViewAccess($downSell);
        $this->access->registerEvent(IntercomEvents::ACCESS_FUNNEL_FORMULA);

        $form = $this->formFactory->create(DownSellType::class, $downSell, [
            'course' => $course,
        ]);
        $added = false;

        $form->handleRequest($request);

        $hasClone = $this->hasFunnelClone($downSell, $course);

        if (!$hasClone) {
            if ($form->isValid()) {
                $course->addSalesFunnelDownSell($downSell);
                $this->entityManager->persist($course);
                $this->entityManager->flush();

                $added = true;
            }
        } else {
            $this->flashBag->add('error', 'Такое предложение уже существует.');
        }


        $content = $this->twig->render('@HBAdmin/SaleFunnel/DownSell/list.html.twig', [
            'form'   => $form->createView(),
            'course' => $course,
            'added'  => $added,
        ]);

        return new Response($content);
    }

    /**
     * @param SalesFunnelDownSell $newFunnel
     * @param Course              $course
     *
     * @return bool
     */
    private function hasFunnelClone(SalesFunnelDownSell $newFunnel, Course $course): bool
    {
        $funnels = $course->getSalesFunnelDownSells();
        $newSectionIds = $this->getSectionsIds($newFunnel);

        /** @var SalesFunnelDownSell $funnel */
        foreach ($funnels as $funnel) {

            if ($funnel->getName() === $newFunnel->getName()) {

                // name is identical, need check sections
                $sectionsIds = $this->getSectionsIds($funnel);

                if ($sectionsIds == $newSectionIds) {
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