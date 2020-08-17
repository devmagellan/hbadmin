<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\Customer\Password;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Component\HashGenerator;
use HB\AdminBundle\Component\Mailer\Mailer;
use HB\AdminBundle\Component\Mailer\Model\Receiver;
use HB\AdminBundle\Component\Mailer\Model\Sender;
use HB\AdminBundle\Component\Model\Email;
use HB\AdminBundle\Entity\Customer;
use HB\AdminBundle\Form\Password\PasswordRecoveryRequestType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;

class RequestRecoveryController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var Mailer
     */
    private $mailer;

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
     * @var string
     */
    private $emailFrom;

    /**
     * RequestRecoveryController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param Mailer                 $mailer
     * @param FormFactoryInterface   $formFactory
     * @param RouterInterface        $router
     * @param \Twig_Environment      $twig
     * @param FlashBagInterface      $flashBag
     * @param string                 $emailFrom
     */
    public function __construct(EntityManagerInterface $entityManager, Mailer $mailer, FormFactoryInterface $formFactory, RouterInterface $router, \Twig_Environment $twig, FlashBagInterface $flashBag, string $emailFrom)
    {
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->twig = $twig;
        $this->flashBag = $flashBag;
        $this->emailFrom = $emailFrom;
    }

    /**
     * @param Request $request
     */
    public function handleAction(Request $request)
    {
        $form = $this->formFactory->create(PasswordRecoveryRequestType::class, null, [
            'action' => $this->router->generate('hb.customer.password.recovery.request'),
        ]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $email = $form->getData()['email'];
            $customer = $this->entityManager->getRepository(Customer::class)->findOneBy([
                'email'  => $email,
                'status' => true,
            ]);

            if ($customer) {
                $hash = HashGenerator::generate();

                $customer->setPasswordRecoveryHash($hash);
                $this->entityManager->persist($customer);
                $this->entityManager->flush();

                $emailTemplate = '@HBAdmin/Password/recovery_letter.html.twig';

                $this->mailer->send(
                    new Sender(new Email($this->emailFrom), 'System'),
                    new Receiver(new Email($email)),
                    $emailTemplate,
                    'Восстановление пароля',
                    [
                        'hash' => $hash,
                    ]
                );

                $this->flashBag->add('success', 'На указанный email было отправлено письмо с инструкциями по восстановлению пароля');

                return new RedirectResponse($this->router->generate('login'));
            } else {
                $form->get('email')->addError(new FormError('Ошибочный email'));
            }
        }

        $content = $this->twig->render('@HBAdmin/Password/recovery_request.html.twig', [
            'form' => $form->createView(),
        ]);

        return new Response($content);
    }
}