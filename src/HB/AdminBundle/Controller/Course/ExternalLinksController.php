<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\Course;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Entity\Customer;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelDownSell;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelWebinar;
use HB\AdminBundle\Form\SaleFunnel\ExternalLinksType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ExternalLinksController
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
     * @var Customer
     */
    private $currentUser;

    /**
     * ExternalLinksController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param FormFactoryInterface   $formFactory
     * @param \Twig_Environment      $twig
     * @param TokenStorageInterface  $tokenStorage
     */
    public function __construct (EntityManagerInterface $entityManager, FormFactoryInterface $formFactory, \Twig_Environment $twig, TokenStorageInterface $tokenStorage)
    {
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
        $this->twig = $twig;
        $this->currentUser = $tokenStorage->getToken()->getUser();
    }

    /**
     * @param Course  $course
     * @param Request $request
     *
     * @return Response
     */
    public function handleAction (Course $course, Request $request)
    {
        $form = $this->formFactory->create(ExternalLinksType::class, null, [
            'course' => $course,
            'isAdmin' => $this->currentUser->isAdmin()
        ]);

        $accessRestriction = false;
        $saved = false;

        $form->handleRequest($request);

        if ($this->currentUser->isAdmin()) {
            if ($form->isValid()) {
                $formData = $form->getData();

                $course->setLinkOnlineSchool($formData[ExternalLinksType::ONLINE_SCHOOL_FIELD]);
                $course->setLinkPaymentPage($formData[ExternalLinksType::PAYMENT_PAGE_FIELD]);

                if ($course->getSalesFunnelOrganic()) {
                    $organic = $course->getSalesFunnelOrganic();
                    $organic->setExternalLink($formData[ExternalLinksType::ORGANIC_FIELD]);
                    $this->entityManager->persist($organic);
                }

                if ($course->getSalesFunnelEducational()) {
                    $educational = $course->getSalesFunnelEducational();
                    $educational->setExternalLink($formData[ExternalLinksType::EDUCATIONAL]);
                    $this->entityManager->persist($educational);
                }

                if ($course->getSalesFunnelWebinar()->count() > 0) {

                    /** @var SalesFunnelWebinar $webinar */
                    foreach ($course->getSalesFunnelWebinar() as $webinar) {
                        $webinar->setExternalLink(
                            $formData[ExternalLinksType::WEBINAR_FIELD.$webinar->getId()]
                        );
                        $this->entityManager->persist($webinar);
                    }
                }

                if ($course->getSalesFunnelDownSells()->count() > 0) {

                    /** @var SalesFunnelDownSell $downSell */
                    foreach ($course->getSalesFunnelDownSells() as $downSell) {
                        $downSell->setExternalLink(
                            $formData[ExternalLinksType::DOWNSELL_FIELD.$downSell->getId()]
                        );
                        $this->entityManager->persist($downSell);
                    }
                }

                $this->entityManager->persist($course);
                $this->entityManager->flush();
                $saved = true;
            }
        } else {
            if ($request->isMethod(Request::METHOD_POST)) {
                $accessRestriction = true;
            }
        }

        $content = $this->twig->render('@HBAdmin/Course/external_links.html.twig', [
            'form'              => $form->createView(),
            'accessRestriction' => $accessRestriction,
            'saved'             => $saved,
        ]);

        return new Response($content);
    }
}