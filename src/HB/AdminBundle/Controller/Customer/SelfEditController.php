<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\Customer;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\Customer;
use HB\AdminBundle\Entity\CustomerPaymentAccount;
use HB\AdminBundle\Exception\CustomerInteractAccessDeniedException;
use HB\AdminBundle\Form\CustomerPaymentAccount\CustomerPaymentAccountBankType;
use HB\AdminBundle\Form\CustomerPaymentAccount\CustomerPaymentAccountPayoneerType;
use HB\AdminBundle\Form\CustomerPaymentAccount\CustomerPaymentAccountPaypalType;
use HB\AdminBundle\Form\CustomerSelfEditType;
use HB\AdminBundle\Service\FormHandler;
use HB\AdminBundle\Service\Intercom\IntercomEventRecorder;
use HB\AdminBundle\Service\Intercom\IntercomEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SelfEditController
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var FormHandler
     */
    private $formHandler;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var Customer
     */
    private $currentUser;

    /**
     * @var IntercomEventRecorder
     */
    private $eventRecorder;

    /**
     * EditController constructor.
     *
     * @param FormFactoryInterface         $formFactory
     * @param FormHandler                  $formHandler
     * @param FlashBagInterface            $flashBag
     * @param RouterInterface              $router
     * @param EntityManagerInterface       $entityManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param \Twig_Environment            $twig
     * @param TokenStorageInterface        $tokenStorage
     * @param IntercomEventRecorder        $eventRecorder
     */
    public function __construct(FormFactoryInterface $formFactory, FormHandler $formHandler, FlashBagInterface $flashBag, RouterInterface $router, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder, \Twig_Environment $twig, TokenStorageInterface $tokenStorage, IntercomEventRecorder $eventRecorder)
    {
        $this->formFactory = $formFactory;
        $this->formHandler = $formHandler;
        $this->flashBag = $flashBag;
        $this->router = $router;
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->twig = $twig;
        $this->currentUser = $tokenStorage->getToken()->getUser();
        $this->eventRecorder = $eventRecorder;
    }


    /**
     * @param Request  $request
     * @param Customer $customer
     *
     * @return Response
     */
    public function handleAction(Request $request): Response
    {
        $customer = $this->currentUser;

        $this->eventRecorder->registerEvent(IntercomEvents::ACCESS_PROFILE);

        $paymentAccount = $customer->getPaymentAccount();

        if (!$customer->getPaymentAccount()) {
            $paymentAccount = new CustomerPaymentAccount($customer);
            $customer->setPaymentAccount($paymentAccount);
        }

        $formAccountPaypal = $this->formFactory->create(CustomerPaymentAccountPaypalType::class, $paymentAccount);
        $formAccountPayoneer = $this->formFactory->create(CustomerPaymentAccountPayoneerType::class, $paymentAccount);
        $formAccountBank = $this->formFactory->create(CustomerPaymentAccountBankType::class, $paymentAccount);

        $form = $this->formFactory->create(CustomerSelfEditType::class, $customer);


        if ($request->isMethod(Request::METHOD_POST)) {

            if ($this->formHandler->handle($customer, $request, $form)) {

                if ($customer->getPlainPassword()) {
                    $passwordHash = $this->passwordEncoder->encodePassword($customer, $customer->getPlainPassword());
                    $customer->setPassword($passwordHash);
                }

                $this->entityManager->persist($customer);
                $this->entityManager->flush();

                $this->flashBag->add('success', 'Пользователь успешно изменен!');
            }

            if ($this->formHandler->handle($paymentAccount, $request, $formAccountPaypal) ||
                $this->formHandler->handle($paymentAccount, $request, $formAccountPayoneer) ||
                $this->formHandler->handle($paymentAccount, $request, $formAccountBank)) {
                $this->flashBag->add('success', 'Платежный аккаунт сохранен');
            }
        }

        $content = $this->twig->render("@HBAdmin/Customer/self_edit.html.twig", [
            'form'                => $form->createView(),
            'customer'            => $customer,
            'formAccountPaypal'   => $formAccountPaypal->createView(),
            'formAccountPayoneer' => $formAccountPayoneer->createView(),
            'formAccountBank'     => $formAccountBank->createView(),
        ]);

        return new Response($content);
    }
}