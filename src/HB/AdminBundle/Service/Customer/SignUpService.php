<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Service\Customer;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Component\HashGenerator;
use HB\AdminBundle\Component\Mailer\Mailer;
use HB\AdminBundle\Component\Mailer\Model\Receiver;
use HB\AdminBundle\Component\Mailer\Model\Sender;
use HB\AdminBundle\Component\Model\Email;
use HB\AdminBundle\Entity\Customer;
use HB\AdminBundle\Entity\CustomerPacket;
use HB\AdminBundle\Entity\CustomerPaymentAccount;
use HB\AdminBundle\Entity\CustomerRole;
use HB\AdminBundle\Entity\Types\CustomerPacketType;
use HB\AdminBundle\Service\SandBoxCourseService;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;

class SignUpService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var UserPasswordEncoder
     */
    private $userPasswordEncoder;

    /**
     * @var SandBoxCourseService
     */
    private $sandBoxCourseService;

    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * @var string
     */
    private $emailFrom;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * SignUpService constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordEncoder    $userPasswordEncoder
     * @param SandBoxCourseService   $sandBoxCourseService
     * @param Mailer                 $mailer
     * @param string                 $emailFrom
     * @param RouterInterface        $router
     */
    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoder $userPasswordEncoder, SandBoxCourseService $sandBoxCourseService, Mailer $mailer, string $emailFrom, RouterInterface $router)
    {
        $this->entityManager = $entityManager;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->sandBoxCourseService = $sandBoxCourseService;
        $this->mailer = $mailer;
        $this->emailFrom = $emailFrom;
        $this->router = $router;
    }


    /**
     * @param Email              $email
     * @param string             $name
     * @param string             $role
     * @param CustomerPacketType $customerPacketType
     * @param string             $plainPassword
     * @param string             $phone
     *
     * @return Customer
     */
    public function signUp(Email $email, string $name, string $role, CustomerPacketType $customerPacketType, string $phone, string $plainPassword = null)
    {
        $role = $this->entityManager->getRepository(CustomerRole::class)->findOneBy(['name' => $role]);
        $packet = $this->entityManager->getRepository(CustomerPacket::class)->findOneBy(['type' => $customerPacketType->getValue()]);

        if (!$role) {
            throw new \InvalidArgumentException('Не найдена роль при создании пользователя. Роль:'.$role);
        }

        if (!$packet) {
            throw new \InvalidArgumentException('Не найдена пакет при создании пользователя. Пакет:'.$customerPacketType->getValue());
        }

        $customer = new Customer();

        $customer->disable();
        $customer->setEmail($email->getValue());
        $customer->setPhone($phone);

        $plainPassword = $plainPassword ?: HashGenerator::generate();

        $password = $this->userPasswordEncoder->encodePassword($customer, $plainPassword);
        $customer->setPassword($password);
        $customer->setFirstName($name);
        $customer->setRole($role);
        $customer->setPacket($packet);
        $customer->setSignupHash(HashGenerator::generate());

        $this->entityManager->persist($customer);
        $this->entityManager->flush();

        if ($customer->isAuthor() || $customer->isProducer()) {
            $this->sandBoxCourseService->create($customer);
        }

        return $customer;
    }

    /**
     * @param Customer $customer
     */
    public function sendFirstSignUpFromTypeFormEmail(Customer $customer)
    {
        $emailTemplate = '@HBAdmin/Mailer/Customer/email_signup_confirm.html.twig';
        $link = $this->router->generate(
            'hb.customer.signup.confirm_data',
            ['hash' => $customer->getSignupHash()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $this->mailer->send(
            new Sender(new Email($this->emailFrom), 'Heartbeat Education | Образовательная Платформа'),
            new Receiver(new Email($customer->getEmail())),
            $emailTemplate,
            $customer->getFirstName().', завершите регистрацию',
            [
                'customer' => $customer,
                'link'     => $link,
            ]
        );
    }
}
