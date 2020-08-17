<?php

declare(strict_types = 1);

namespace HB\AdminBundle\Controller\SaleFunnel\Educational;

use HB\AdminBundle\Entity\SaleFunnel\Educational\Letter;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelEducational;
use HB\AdminBundle\Form\SaleFunnel\Educational\LetterType;
use HB\AdminBundle\Service\FormHandler;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class EditLetterController
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var FormHandler
     */
    private $formHandler;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * EditNameController constructor.
     *
     * @param RouterInterface      $router
     * @param FormHandler          $formHandler
     * @param FormFactoryInterface $formFactory
     * @param \Twig_Environment    $twig
     */
    public function __construct(RouterInterface $router, FormHandler $formHandler, FormFactoryInterface $formFactory, \Twig_Environment $twig)
    {
        $this->router = $router;
        $this->formHandler = $formHandler;
        $this->formFactory = $formFactory;
        $this->twig = $twig;
    }

    /**
     * @param SalesFunnelEducational $funnel
     * @param Letter                 $letter
     * @param Request                $request
     *
     * @return RedirectResponse|Response
     */
    public function handleAction(SalesFunnelEducational $funnel, Letter $letter, Request $request)
    {
        $form = $this->formFactory->create(LetterType::class, $letter);

        if ($this->formHandler->handle($letter, $request, $form)) {
            return new RedirectResponse($this->router->generate('hb.sale_funnel.educational.edit', ['id' => $funnel->getId()]));
        }

        return new Response($this->twig->render('@HBAdmin/SaleFunnel/Educational/edit_name.html.twig', [
            'funnel' => $funnel,
            'letter' => $letter,
            'form'   => $form->createView(),
        ]));
    }
}