<?php

declare(strict_types = 1);


namespace HB\AuthorBundle\Controller\Index;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Component\Json;
use HB\AdminBundle\Entity\Customer;
use HB\AdminBundle\Entity\CustomerPacket;
use HB\AdminBundle\Entity\CustomerRole;
use HB\AdminBundle\Entity\Types\CustomerPacketType;
use HB\AuthorBundle\Form\CustomerSignUpType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SignUpController
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
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * SignUpController constructor.
     *
     * @param EntityManagerInterface       $entityManager
     * @param FormFactoryInterface         $formFactory
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param SessionInterface             $session
     * @param TokenStorageInterface        $tokenStorage
     * @param \Twig_Environment            $twig
     * @param FlashBagInterface            $flashBag
     */
    public function __construct(EntityManagerInterface $entityManager, FormFactoryInterface $formFactory, UserPasswordEncoderInterface $passwordEncoder, SessionInterface $session, TokenStorageInterface $tokenStorage, \Twig_Environment $twig, FlashBagInterface $flashBag)
    {
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
        $this->passwordEncoder = $passwordEncoder;
        $this->session = $session;
        $this->tokenStorage = $tokenStorage;
        $this->twig = $twig;
        $this->flashBag = $flashBag;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function handleAction(Request $request)
    {
        $customer = new Customer();
        $defaultPacket = $this->entityManager->getRepository(CustomerPacket::class)->findOneBy(['type' => CustomerPacketType::CUSTOM]);
        $customer->setPacket($defaultPacket);
        $this->setAuthorRole($customer);

        $form = $this->formFactory->create(CustomerSignUpType::class, $customer);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $plainPassword = $form->get('plainPassword')->getData();
            $customer->setPassword($this->passwordEncoder->encodePassword($customer, $plainPassword));

            $packet = $form->get('packetPlain')->getData();

            $packetDB = $this->entityManager->getRepository(CustomerPacket::class)->findOneBy(['type' => $packet]);

            if (!$packetDB) {
                throw new \InvalidArgumentException('Customer packet not found exception');
            } else {
                $customer->setPacket($packetDB);
            }

            if ($form->isValid()) {

                $this->entityManager->persist($customer);
                $this->entityManager->flush();

                $token = new UsernamePasswordToken($customer, null, 'main', $customer->getRoles());
                $this->tokenStorage->setToken($token);
                $this->session->set('_security_main', serialize($token));
                $this->flashBag->add('success', 'Спасибо за регистрацию!');

                return Json::ok('registered');
                //return new RedirectResponse($this->router->generate('hb.courses.list'));
            }
        }

        $content = $this->twig->render("@HBAuthor/Default/sign_up_form.html.twig", [
            'form' => $form->createView(),
        ]);

        return new Response($content);
    }


    /**
     * @param Customer $customer
     */
    private function setAuthorRole(Customer $customer)
    {
        $authorRole = $this->entityManager->getRepository(CustomerRole::class)->findOneBy(['name' => CustomerRole::ROLE_AUTHOR]);
        $customer->setRole($authorRole);
    }
}