<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\Customer\Password;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\Customer;
use HB\AdminBundle\Form\Password\PasswordRecoveryType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;

class RecoveryController
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
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * @var UserPasswordEncoder
     */
    private $passwordEncoder;

    /**
     * RecoveryController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param FormFactoryInterface   $formFactory
     * @param RouterInterface        $router
     * @param \Twig_Environment      $twig
     * @param FlashBagInterface      $flashBag
     * @param UserPasswordEncoder    $passwordEncoder
     */
    public function __construct(EntityManagerInterface $entityManager, FormFactoryInterface $formFactory, RouterInterface $router, \Twig_Environment $twig, FlashBagInterface $flashBag, UserPasswordEncoder $passwordEncoder)
    {
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->twig = $twig;
        $this->flashBag = $flashBag;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param string  $hash
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function handleAction(string $hash, Request $request)
    {
        $customer = $this->entityManager->getRepository(Customer::class)->findOneBy(['passwordRecoveryHash' => $hash]);

        if (!$customer) {
            $this->flashBag->add('error', 'Ошибка восстановления пароля!');

            return new RedirectResponse($this->router->generate('login'));
        }

        $form = $this->formFactory->create(PasswordRecoveryType::class, null, [
            'action' => $this->router->generate('hb.customer.password.recovery', ['hash' => $hash]),
        ]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $password = $form->getData()['password'];

            $customer->setPasswordRecoveryHash(null);
            $customer->setPassword(
                $this->passwordEncoder->encodePassword($customer, $password)
            );

            $this->entityManager->persist($customer);
            $this->entityManager->flush();

            $this->flashBag->add('success', 'Пароль успешно изменен. Воспользуйтесь формой входа');

            return new RedirectResponse($this->router->generate('login'));
        }


        $content = $this->twig->render('@HBAdmin/Password/recovery_form.html.twig', [
            'form' => $form->createView(),
        ]);

        return new Response($content);
    }
}