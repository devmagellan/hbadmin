<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\Customer\SignUp;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\Customer;
use HB\AdminBundle\Form\Customer\ConfirmSignUpDataType;
use HB\AdminBundle\Service\SmsSender;
use HB\AdminBundle\Service\ZapierEventCollector;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ConfirmDataController
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
     * @var RouterInterface
     */
    private $router;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var SmsSender
     */
    private $smsSender;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;

    /**
     * @var ZapierEventCollector
     */
    private $zapierEventCollector;

    /**
     * ConfirmDataController constructor.
     *
     * @param EntityManagerInterface       $entityManager
     * @param FormFactoryInterface         $formFactory
     * @param RouterInterface              $router
     * @param FlashBagInterface            $flashBag
     * @param \Twig_Environment            $twig
     * @param SmsSender                    $smsSender
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     * @param ZapierEventCollector $zapierEventCollector
     */
    public function __construct(EntityManagerInterface $entityManager, FormFactoryInterface $formFactory, RouterInterface $router, FlashBagInterface $flashBag, \Twig_Environment $twig, SmsSender $smsSender, UserPasswordEncoderInterface $userPasswordEncoder, ZapierEventCollector $zapierEventCollector)
    {
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->flashBag = $flashBag;
        $this->twig = $twig;
        $this->smsSender = $smsSender;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->zapierEventCollector = $zapierEventCollector;
    }

    /**
     * @param string  $hash
     * @param Request $request
     *
     * @return Response
     */
    public function handleAction(string $hash, Request $request)
    {
        $customer = $this->entityManager->getRepository(Customer::class)->findOneBy([
            'signupHash' => $hash,
            'status'     => false,
        ]);

        if (!$customer) {
            $this->flashBag->add('error', 'Ошибочная ссылка');

            return new RedirectResponse($this->router->generate('login'));
        }

        $form = $this->formFactory->create(ConfirmSignUpDataType::class, $customer);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $plainPassword = $form->get('plainPassword')->getData();
            $password = $this->userPasswordEncoder->encodePassword($customer, $plainPassword);
            $customer->setPassword($password);

            $this->entityManager->persist($customer);
            $this->entityManager->flush();

            //$this->zapierEventCollector->passwordConfirmed($customer, $plainPassword);

            return $this->temporaryWithoutSms($customer);
            /*$this->smsSender->sendSignUpSms($customer);

            return new RedirectResponse($this->router->generate('hb.customer.signup.confirm_sms', [
                'hash' => $hash,
            ]));*/
        }

        $content = $this->twig->render('@HBAdmin/Login/signup_data_confirm.index.html.twig', [
            'form' => $form->createView(),
            'hash' => $hash,
        ]);

        return new Response($content);
    }

    /**
     * @param Customer $customer
     *
     * @return RedirectResponse
     */
    private function temporaryWithoutSms(Customer $customer)
    {
        $customer->enable();
        $customer->setSignupHash(null);
        $customer->setConfirmSmsCode(null);
        $this->entityManager->persist($customer);
        $this->entityManager->flush();

        $this->flashBag->add('success', 'Спасибо за регистрацию. Войдите используя email и пароль, указанные при регистрации.');

        return new RedirectResponse($this->router->generate('login'));
    }
}