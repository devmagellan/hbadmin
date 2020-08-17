<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\Finance\Payment\Admin;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\Customer;
use HB\AdminBundle\Entity\CustomerPayment;
use HB\AdminBundle\Form\Finance\PaymentType;
use HB\AdminBundle\Service\FormHandler;
use HB\AdminBundle\Service\Intercom\IntercomEventRecorder;
use HB\AdminBundle\Service\Intercom\IntercomEvents;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @IsGranted("ROLE_ADMIN")
 */
class PaymentListController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

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
     * @var IntercomEventRecorder
     */
    private $eventRecorder;

    /**
     * @var Customer
     */
    private $currentUser;

    /**
     * PaymentListController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param \Twig_Environment      $twig
     * @param FormFactoryInterface   $formFactory
     * @param RouterInterface        $router
     * @param FormHandler            $formHandler
     * @param FlashBagInterface      $flashBag
     * @param IntercomEventRecorder  $eventRecorder
     * @param TokenStorageInterface  $tokenStorage
     */
    public function __construct(EntityManagerInterface $entityManager, \Twig_Environment $twig, FormFactoryInterface $formFactory, RouterInterface $router, FormHandler $formHandler, FlashBagInterface $flashBag, IntercomEventRecorder $eventRecorder, TokenStorageInterface $tokenStorage)
    {
        $this->entityManager = $entityManager;
        $this->twig = $twig;
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->formHandler = $formHandler;
        $this->flashBag = $flashBag;
        $this->eventRecorder = $eventRecorder;
        $this->currentUser = $tokenStorage->getToken()->getUser();
    }

    /**
     * @param Request  $request
     *
     * @return Response
     */
    public function handleAction(Request $request)
    {
        $this->eventRecorder->registerEvent(IntercomEvents::ACCESS_REPORTS);

        $payment = new CustomerPayment();
        $payment->setCreatorId($this->currentUser->getId());

        if ($id = $request->get('id', null)) {
            $customer = $this->entityManager->getRepository(Customer::class)->find($id);
            $payment->setCustomer($customer);
        }

        $form = $this->formFactory->create(PaymentType::class, $payment, [
            'action' => $this->router->generate('hb.finance.admin.payments'),
            'method' => Request::METHOD_POST,
        ]);

        if ($this->formHandler->handle($payment, $request, $form)) {
            $this->flashBag->add('success', 'Платеж сохранен');

            return new RedirectResponse($this->router->generate('hb.finance.admin.payments'));
        }

        /** @var CustomerPayment[] $payments */
        $payments = $this->entityManager->getRepository(CustomerPayment::class)->findBy([], ['paymentDate' => 'ASC']);

        $content = $this->twig->render('@HBAdmin/Finance/Payment/Admin/payment_list.html.twig', [
            'payments' => $payments,
            'form'     => $form->createView(),
        ]);

        return new Response($content);
    }
}