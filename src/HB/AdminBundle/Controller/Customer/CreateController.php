<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\Customer;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\Customer;
use HB\AdminBundle\Entity\CustomerPacket;
use HB\AdminBundle\Entity\CustomerRole;
use HB\AdminBundle\Entity\UploadCareFile;
use HB\AdminBundle\Form\CustomerType;
use HB\AdminBundle\Service\CustomerAccessService;
use HB\AdminBundle\Service\FormHandler;
use HB\AdminBundle\Service\ImageManager;
use HB\AdminBundle\Service\SandBoxCourseService;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CreateController
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var ImageManager
     */
    private $imageManager;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var FormHandler
     */
    private $formHandler;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var Customer
     */
    private $currentUser;

    /**
     * @var CustomerAccessService
     */
    private $customerAccessService;

    /**
     * @var SandBoxCourseService
     */
    private $sandBoxCourseService;

    /**
     * CreateController constructor.
     *
     * @param FormFactoryInterface         $formFactory
     * @param ImageManager                 $imageManager
     * @param FlashBagInterface            $flashBag
     * @param RouterInterface              $router
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param FormHandler                  $formHandler
     * @param EntityManagerInterface       $entityManager
     * @param \Twig_Environment            $twig
     * @param TokenStorageInterface        $tokenStorage
     * @param CustomerAccessService        $customerAccessService
     * @param SandBoxCourseService         $sandBoxCourseService
     */
    public function __construct(FormFactoryInterface $formFactory, ImageManager $imageManager, FlashBagInterface $flashBag, RouterInterface $router, UserPasswordEncoderInterface $passwordEncoder, FormHandler $formHandler, EntityManagerInterface $entityManager, \Twig_Environment $twig, TokenStorageInterface $tokenStorage, CustomerAccessService $customerAccessService, SandBoxCourseService $sandBoxCourseService)
    {
        $this->formFactory = $formFactory;
        $this->imageManager = $imageManager;
        $this->flashBag = $flashBag;
        $this->router = $router;
        $this->passwordEncoder = $passwordEncoder;
        $this->formHandler = $formHandler;
        $this->entityManager = $entityManager;
        $this->twig = $twig;
        $this->currentUser = $tokenStorage->getToken()->getUser();
        $this->customerAccessService = $customerAccessService;
        $this->sandBoxCourseService = $sandBoxCourseService;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function handleAction(Request $request): Response
    {
        $customer = new Customer();
        $customer->setOwner($this->currentUser);

        $authorsQuery = null;
        if ($this->currentUser->hasRole(CustomerRole::ROLE_PRODUCER)) {
            $customer->setProducer($this->currentUser);
            $authorsQuery = $this->customerAccessService->getProducerAuthorsQuery($this->currentUser);
        }

        $canChooseCustomerPacket = $this->canChooseCustomerPacket($this->currentUser);
        $packetForCustomer = $this->getPacketForCustomer($this->currentUser);

        if (!$canChooseCustomerPacket) {
            $customer->setPacket($packetForCustomer);
        }

        $form = $this->formFactory->create(CustomerType::class, $customer, [
            'roles_query'       => $this->customerAccessService->resolveCustomerRolesAvailableForCurrentUserQuery(),
            'authors_query'     => $authorsQuery,
            'can_choose_packet' => $canChooseCustomerPacket,
            'packet'            => $packetForCustomer,
        ]);

        if ($request->isMethod(Request::METHOD_POST)) {

            /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $photo */
            $photoCdn = $request->get('hb_adminbundle_customer')['photo_cdn'];
            $photoUuid = $request->get('hb_adminbundle_customer')['photo_uuid'];
            $photoName = $request->get('hb_adminbundle_customer')['photo_name'];

            if ($photoCdn && $photoUuid && $photoName) {
                $photoFile = new UploadCareFile($photoUuid, $photoCdn, $photoName);
                $this->entityManager->persist($photoFile);
                $customer->setPhoto($photoFile);
            }

            $password = $request->get('hb_adminbundle_customer')['plainPassword']['first'];
            $passwordHash = $this->passwordEncoder->encodePassword($customer, $password);
            $customer->setPassword($passwordHash);

            if ($this->formHandler->handle($customer, $request, $form)) {
                try {

                    if ($this->currentUser->hasRole(CustomerRole::ROLE_AUTHOR) && $customer->hasRole(CustomerRole::ROLE_MANAGER)) {
                        $customer->addAuthor($this->currentUser);
                    }

                    $this->entityManager->flush();

                    if ($customer->isAuthor() || $customer->isProducer()) {
                        $this->sandBoxCourseService->create($customer);
                    }

                    $this->flashBag->add('success', 'Пользователь успешно создан!');

                    return new RedirectResponse($this->router->generate("hb.customer.list"));
                } catch (\Exception $ex) {
                    $this->flashBag->add('error', 'Ошибка при сохранении пользователя!');

                    return new RedirectResponse($this->router->generate("hb.customer.create"));
                }

            } else {
                $this->flashBag->add('error', 'Ошибка при сохранении пользователя!');
            }
        }

        $content = $this->twig->render("@HBAdmin/Customer/create.html.twig", [
            'form'                    => $form->createView(),
            'canChooseCustomerPacket' => $canChooseCustomerPacket,
        ]);

        return new Response($content);
    }

    /**
     * @param Customer $currentCustomer
     *
     * @return CustomerPacket | null
     */
    private function getPacketForCustomer(Customer $currentCustomer): ?CustomerPacket
    {
        $packet = null;
        $currentCustomerOwner = $currentCustomer->getOwner();

        if ($currentCustomer->isAuthor()) {
            if ($currentCustomerOwner && $currentCustomerOwner->isProducer()) {
                $packet = $currentCustomerOwner->getPacket();
            } else {
                $packet = $currentCustomer->getPacket();
            }
        } else if ($currentCustomer->isProducer()) {
            $packet = $currentCustomer->getPacket();
        } else if ($currentCustomer->isManager()) {
            throw new \InvalidArgumentException('Менеджер не может создать пользователя');
        }

        return $packet;
    }

    /**
     * @param Customer $currentUser
     *
     * @return bool
     */
    private function canChooseCustomerPacket(Customer $currentUser)
    {
        if ($currentUser->isProducer() || $currentUser->isAuthor()) {
            return false;
        }

        if ($currentUser->isManager()) {
            throw new \InvalidArgumentException('Менеджер не может создать пользователя');
        }

        return true;
    }

}