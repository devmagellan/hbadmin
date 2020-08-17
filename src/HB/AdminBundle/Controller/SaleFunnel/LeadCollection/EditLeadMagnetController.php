<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\LeadCollection;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelLeadCollection;
use HB\AdminBundle\Form\SaleFunnel\LeadCollection\LeadCollectionType;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\FormHandler;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;

class EditLeadMagnetController
{
    /**
     * @var FormHandler
     */
    private $formHandler;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var FormFactory
     */
    private $formFactory;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var CourseAccessService
     */
    private $access;

    /**
     * EditController constructor.
     *
     * @param FormHandler            $formHandler
     * @param RouterInterface        $router
     * @param FormFactory            $formFactory
     * @param FlashBagInterface      $flashBag
     * @param \Twig_Environment      $twig
     * @param EntityManagerInterface $entityManager
     * @param CourseAccessService    $access
     */
    public function __construct(FormHandler $formHandler, RouterInterface $router, FormFactory $formFactory, FlashBagInterface $flashBag, \Twig_Environment $twig, EntityManagerInterface $entityManager, CourseAccessService $access)
    {
        $this->formHandler = $formHandler;
        $this->router = $router;
        $this->formFactory = $formFactory;
        $this->flashBag = $flashBag;
        $this->twig = $twig;
        $this->entityManager = $entityManager;
        $this->access = $access;
    }


    /**
     * @param SalesFunnelLeadCollection $funnel
     * @param Request                   $request
     *
     * @return Response
     */
    public function handleAction(SalesFunnelLeadCollection $funnel, Request $request)
    {
        $this->access->resolveUpdateAccess($funnel);

        $course = $funnel->getCourse();

        $form = $this->formFactory->create(LeadCollectionType::class, $funnel);

        if ($request->isMethod(Request::METHOD_POST)) {
            $lead_magnets_use = $this->resolveLeadMagnetsUse($course);
            $lead_magnets_violation = $this->leadMagnetViolation($lead_magnets_use);

            if ($lead_magnets_violation) {
                $this->flashBag->add('error', 'Добавление невозможно, нет свободных лид магнитов.');

                return new RedirectResponse($request->headers->get('referer'));
            }

            if ($this->formHandler->handle($funnel, $request, $form)) {
                $this->flashBag->add('success', 'Сохранено');

                return new RedirectResponse($this->router->generate('hb.sale_funnel.lead_collection.edit', ['id' => $course->getId()]));
            }
        }

        $lead_magnets_use = $this->resolveLeadMagnetsUse($course);
        $lead_magnets_violation = $this->leadMagnetViolation($lead_magnets_use);

        $content = $this->twig->render('@HBAdmin/SaleFunnel/LeadCollection/edit_lead_magnet.html.twig', [
            'course'                 => $course,
            'funnel'                 => $funnel,
            'form'                   => $form->createView(),
            'lead_magnets_use'       => $lead_magnets_use,
            'lead_magnets_violation' => $lead_magnets_violation,
        ]);

        return new Response($content);
    }

    /**
     * @param Course $course
     *
     * @return array
     */
    private function resolveLeadMagnetsUse(Course $course): array
    {
        $leadMagnetsFields = ['leadMagnetOrganic', 'leadMagnetWebinar', 'leadMagnetEducation', 'leadMagnetDownSell', 'leadMagnetLayerCake'];

        $exist = [];

        foreach ($leadMagnetsFields as $field) {
            $exist[$field] = $this->isLeadMagnetInUse($course, $field);
        }

        return $exist;
    }

    /**
     * @param Course $course
     * @param string $leadMagnetField
     *
     * @return bool
     */
    private function isLeadMagnetInUse(Course $course, string $leadMagnetField)
    {
        return (bool) $this->entityManager->createQueryBuilder()
            ->select('1')
            ->from(SalesFunnelLeadCollection::class, 'funnel')
            ->leftJoin('funnel.course', 'course')
            ->where('course.id = :course_id')
            ->andWhere('funnel.'.$leadMagnetField.' = true')
            ->setParameters([
                'course_id' => $course->getId(),
            ])
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
    }

    /**
     * Check if all of lead magnets is in use
     *
     * @param array $leadMagnetsUse
     *
     * @return bool
     */
    private function leadMagnetViolation(array $leadMagnetsUse): bool
    {
        foreach ($leadMagnetsUse as $leadMagnet) {
            if (!$leadMagnet) {
                return false;
            }
        }

        return true;
    }
}