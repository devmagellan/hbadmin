<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\Customer\SignUp;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\Customer;
use HB\AdminBundle\Form\Customer\ConfirmSmsType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;

class ConfirmSmsController
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
     * @var RouterInterface
     */
    private $router;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * ConfirmSmsController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param \Twig_Environment      $twig
     * @param RouterInterface        $router
     * @param FlashBagInterface      $flashBag
     * @param FormFactoryInterface   $formFactory
     */
    public function __construct(EntityManagerInterface $entityManager, \Twig_Environment $twig, RouterInterface $router, FlashBagInterface $flashBag, FormFactoryInterface $formFactory)
    {
        $this->entityManager = $entityManager;
        $this->twig = $twig;
        $this->router = $router;
        $this->flashBag = $flashBag;
        $this->formFactory = $formFactory;
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
        ]);

        $form = $this->formFactory->create(ConfirmSmsType::class);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $code = $form->getData()['code'];

            if ($code === $customer->getConfirmSmsCode()) {
                $customer->enable();
                $customer->setSignupHash(null);
                $customer->setConfirmSmsCode(null);
                $this->entityManager->persist($customer);
                $this->entityManager->flush();

                $this->flashBag->add('success', 'Спасибо за регистрацию. Войдите используя email и пароль, указанные при регистрации.');

                return new RedirectResponse($this->router->generate('login'));
            }
        }

        $content = $this->twig->render('@HBAdmin/Login/signup_sms_confirm.index.html.twig', [
            'hash' => $hash,
            'form' => $form->createView(),
        ]);

        return new Response($content);
    }
}