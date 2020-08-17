<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\Customer;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Entity\Customer;
use HB\AdminBundle\Entity\CustomerPacket;
use HB\AdminBundle\Entity\CustomerRole;
use HB\AdminBundle\Entity\Teachable\TeachableTransaction;
use HB\AdminBundle\Entity\Types\CustomerPacketType;
use HB\AdminBundle\Form\CustomerEditType;
use HB\AdminBundle\Form\CustomerPaymentAccount\CustomerPaymentAccountBankType;
use HB\AdminBundle\Form\CustomerPaymentAccount\CustomerPaymentAccountPayoneerType;
use HB\AdminBundle\Form\CustomerPaymentAccount\CustomerPaymentAccountPaypalType;
use HB\AdminBundle\Helper\ArrayHelper;
use HB\AdminBundle\Service\CustomerAccessService;
use HB\AdminBundle\Service\FormHandler;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class EditController
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
     * @var CustomerAccessService
     */
    private $customerAccessService;

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
     * @param CustomerAccessService        $customerAccessService
     */
    public function __construct(FormFactoryInterface $formFactory, FormHandler $formHandler, FlashBagInterface $flashBag, RouterInterface $router, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder, \Twig_Environment $twig, TokenStorageInterface $tokenStorage, CustomerAccessService $customerAccessService)
    {
        $this->formFactory = $formFactory;
        $this->formHandler = $formHandler;
        $this->flashBag = $flashBag;
        $this->router = $router;
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->twig = $twig;
        $this->currentUser = $tokenStorage->getToken()->getUser();
        $this->customerAccessService = $customerAccessService;
    }


    /**
     * @param Request  $request
     * @param Customer $customer
     *
     * @return Response
     */
    public function handleAction(Request $request, Customer $customer): Response
    {
        $this->customerAccessService->resolveCustomerInteractAccess($customer);

        $authorsQuery = null;
        if ($this->currentUser->hasRole(CustomerRole::ROLE_PRODUCER)) {
            $customer->setProducer($this->currentUser);
            $authorsQuery = $this->customerAccessService->getProducerAuthorsQuery($this->currentUser);
        }

        $canChooseCustomerPacket = $this->canChooseCustomerPacket($this->currentUser);

        $form = $this->formFactory->create(CustomerEditType::class, $customer, [
            'roles_query'       => $this->customerAccessService->resolveCustomerRolesAvailableForCurrentUserQuery(),
            'authors_query'     => $authorsQuery,
            'can_choose_packet' => $canChooseCustomerPacket,
        ]);

        $paymentAccount = $customer->getPaymentAccount();
        $formAccountPaypal = $this->formFactory->create(CustomerPaymentAccountPaypalType::class, $paymentAccount);
        $formAccountPayoneer = $this->formFactory->create(CustomerPaymentAccountPayoneerType::class, $paymentAccount);
        $formAccountBank = $this->formFactory->create(CustomerPaymentAccountBankType::class, $paymentAccount);

        $oldPacketSubscription = $customer->hasPacketSubscription();
        if ($request->isMethod(Request::METHOD_POST)) {

            if ($this->formHandler->handle($customer, $request, $form)) {

                if ($customer->getPlainPassword()) {
                    $passwordHash = $this->passwordEncoder->encodePassword($customer, $customer->getPlainPassword());
                    $customer->setPassword($passwordHash);
                }

                $this->entityManager->persist($customer);
                $this->entityManager->flush();

                $this->flashBag->add('success', 'Пользователь успешно изменен!');
                if ($customer->hasPacketSubscription() !== $oldPacketSubscription) {
                    $this->updateCustomerCoursesTransactions($customer);
                }
            } else {
                $this->flashBag->add('error', 'Ошибка при сохранении пользователя!');
            }
        }

        $content = $this->twig->render("@HBAdmin/Customer/edit.html.twig", [
            'form'                       => $form->createView(),
            'customer'                   => $customer,
            'can_choose_customer_packet' => $canChooseCustomerPacket,
            'formAccountPaypal'          => $formAccountPaypal->createView(),
            'formAccountPayoneer'        => $formAccountPayoneer->createView(),
            'formAccountBank'            => $formAccountBank->createView(),
            'subscriptionPackets'        => $this->getPacketsIdWithPossibleSubscription(),
        ]);

        return new Response($content);
    }


    /**
     * @param Customer $currentUser
     *
     * @return bool
     */
    private function canChooseCustomerPacket(Customer $currentUser)
    {
        if (!$currentUser->isAdmin()) {
            return false;
        }

        return true;
    }

    /**
     * Get packets ids with possible subscription
     *
     * @return array
     */
    private function getPacketsIdWithPossibleSubscription()
    {
        $ids = $this->entityManager->createQueryBuilder()
            ->select('p.id')
            ->from(CustomerPacket::class, 'p')
            ->where('p.type IN (:types)')
            ->setParameter('types', [
                CustomerPacketType::PROFESSIONAL,
                CustomerPacketType::PREMIUM,
                CustomerPacketType::VIP,
            ])
            ->getQuery()->getResult();
        $ids = ArrayHelper::getArrayByIndex($ids, 'id');

        return $ids;
    }

    /**
     * If packet subscription changes - recalculate transactions
     *
     * @param Customer $customer
     */
    private function updateCustomerCoursesTransactions(Customer $customer)
    {
        $courses = $this->entityManager->getRepository(Course::class)->getCoursesQuery($customer)->getResult();

        $externalIds = [];

        /** @var Course $course */
        foreach ($courses as $course) {
            if ($course->getTeachableId()) {
                $externalIds[] = $course->getTeachableId();
            }
        }

        $transactions = $this->entityManager
            ->createQueryBuilder()
            ->select('t')
            ->from(TeachableTransaction::class, 't')
            ->where('t.course_id in (:ids)')
            ->setParameter('ids', $externalIds)
            ->getQuery()->getResult();

        /** @var TeachableTransaction $transaction */
        foreach ($transactions as $transaction) {
            $transaction->updateIncome();
            $this->entityManager->persist($transaction);
        }

        $this->entityManager->flush();
    }
}