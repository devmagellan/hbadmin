<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\Finance\Payment\Admin;


use HB\AdminBundle\Entity\CustomerPayment;
use HB\AdminBundle\Form\Finance\PaymentType;
use HB\AdminBundle\Service\FormHandler;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * @IsGranted("ROLE_ADMIN")
 */
class PaymentEditController
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
     * @var RouterInterface
     */
    private $router;

    /**
     * @var FormHandler
     */
    private $formHandler;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * PaymentEditController constructor.
     *
     * @param \Twig_Environment    $twig
     * @param FormFactoryInterface $formFactory
     * @param RouterInterface      $router
     * @param FormHandler          $formHandler
     * @param FlashBagInterface    $flashBag
     */
    public function __construct(\Twig_Environment $twig, FormFactoryInterface $formFactory, RouterInterface $router, FormHandler $formHandler, FlashBagInterface $flashBag)
    {
        $this->twig = $twig;
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->formHandler = $formHandler;
        $this->flashBag = $flashBag;
    }

    /**
     * @param CustomerPayment $payment
     * @param Request         $request
     *
     * @return Response
     */
    public function handleAction(CustomerPayment $payment, Request $request)
    {
        $form = $this->formFactory->create(PaymentType::class, $payment, [
            'action' => $this->router->generate('hb.finance.admin.payment.edit', ['id' => $payment->getId()]),
            'method' => Request::METHOD_POST,
        ]);

        if ($this->formHandler->handle($payment, $request, $form)) {
            $this->flashBag->add('success', 'Платеж сохранен');

            return new RedirectResponse($this->router->generate('hb.finance.admin.payments'));
        }

        $content = $this->twig->render('@HBAdmin/Finance/Payment/Admin/payment_edit.html.twig', [
            'form'     => $form->createView(),
        ]);

        return new Response($content);
    }
}