<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\Customer;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Component\Mailer\MailerInterface;
use HB\AdminBundle\Component\Mailer\Model\Receiver;
use HB\AdminBundle\Component\Mailer\Model\Sender;
use HB\AdminBundle\Component\Model\Email;
use HB\AdminBundle\Entity\Customer;
use HB\AdminBundle\Entity\CustomerPacket;
use HB\AdminBundle\Entity\Types\CustomerPacketType;
use HB\AdminBundle\Exception\CustomerInteractAccessDeniedException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ChangePacketController
{
    /**
     * @var Customer
     */
    private $currentUser;

    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * @var string
     */
    private $emailFrom;

    /**
     * @var string
     */
    private $emailsTo;

    /**
     * RequestPacketController constructor.
     *
     * @param TokenStorageInterface  $tokenStorage
     * @param MailerInterface        $mailer
     * @param EntityManagerInterface $entityManager
     * @param RouterInterface        $router
     * @param FlashBagInterface      $flashBag
     * @param string                 $emailFrom
     * @param string                 $emailsTo
     */
    public function __construct(TokenStorageInterface $tokenStorage, MailerInterface $mailer, EntityManagerInterface $entityManager, RouterInterface $router, FlashBagInterface $flashBag, string $emailFrom, string $emailsTo)
    {
        $this->currentUser = $tokenStorage->getToken()->getUser();
        $this->mailer = $mailer;
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->flashBag = $flashBag;
        $this->emailFrom = $emailFrom;
        $this->emailsTo = $emailsTo;
    }

    /**
     * @param Customer $customer
     * @param string   $type
     * @param Request  $request
     *
     * @return RedirectResponse
     * @throws CustomerInteractAccessDeniedException
     */
    public function handleAction(Customer $customer, string $type, Request $request)
    {
        $referer = $request->headers->get('referer', $this->router->generate('hb.customer.self.edit'));

        if ($customer->getId() !== $this->currentUser->getId()) {
            throw new CustomerInteractAccessDeniedException('Current user cannot change customer packet');
        }

        $packet = new CustomerPacketType($type);

        $dbPacket = $this->entityManager->getRepository(CustomerPacket::class)->findOneBy(['type' => $packet->getValue()]);

        $packetValue = $packet->getValue();

        if ($packetValue === CustomerPacketType::VIP || $packetValue === CustomerPacketType::CUSTOM) {
            if ($customer->getRequestedPacket() && $customer->getRequestedPacket()->getId() === $dbPacket->getId()) {
                throw new \LogicException('Customer packet was already requested');
            }

            $customer->setRequestedPacket($dbPacket);

            $emailsTo = explode(',', $this->emailsTo);
            $emailTemplate = '@HBAdmin/Mailer/Customer/request_packet.html.twig';

            foreach ($emailsTo as $emailTo) {
                $emailTo = trim($emailTo);

                $this->mailer->send(
                    new Sender(new Email($this->emailFrom), 'System'),
                    new Receiver(new Email($emailTo)),
                    $emailTemplate,
                    'Запрос изменения пакета',
                    [
                        'customer'         => $customer,
                        'packet_previous'  => CustomerPacketType::getName($customer->getPacket()->getType()),
                        'packet_requested' => CustomerPacketType::getName($packetValue),
                    ]
                );
            }

            $this->flashBag->add('success', 'Запрос на изменение пакета был отправлен. Нам менеджер свяжется с Вами в ближайшее время.');
        } else {
            $customer->setPacket($dbPacket);
            $this->flashBag->add('success', 'Пакет изменен');
        }

        $this->entityManager->persist($customer);
        $this->entityManager->flush();

        return new RedirectResponse($referer);
    }
}
