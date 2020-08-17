<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\Customer;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\Customer;
use HB\AdminBundle\Entity\UploadCareFile;
use HB\AdminBundle\Service\CustomerAccessService;
use HB\AdminBundle\Service\Intercom\IntercomEventRecorder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UpdatePhotoController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var CustomerAccessService
     */
    private $customerAccessService;

    /**
     * @var Customer
     */
    private $currentCustomer;

    /**
     * @var IntercomEventRecorder
     */
    private $eventRecorder;

    /**
     * UpdatePhotoController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param CustomerAccessService  $customerAccessService
     * @param TokenStorageInterface  $tokenStorage
     * @param IntercomEventRecorder  $eventRecorder
     */
    public function __construct(EntityManagerInterface $entityManager, CustomerAccessService $customerAccessService, TokenStorageInterface $tokenStorage, IntercomEventRecorder $eventRecorder)
    {
        $this->entityManager = $entityManager;
        $this->customerAccessService = $customerAccessService;
        $this->currentCustomer = $tokenStorage->getToken()->getUser();
        $this->eventRecorder = $eventRecorder;
    }


    /**
     * @param Customer $customer
     * @param Request  $request
     *
     * @return JsonResponse
     */
    public function handleAction(Customer $customer, Request $request): JsonResponse
    {
        if ($this->currentCustomer->getId() !== $customer->getId()) {
            $this->customerAccessService->resolveCustomerInteractAccess($customer);
        }

        $photoCdn = $request->get('cdn');
        $photoUuid = $request->get('uuid');
        $photoName = $request->get('file_name');

        if ($photoCdn && $photoUuid && $photoName) {
            $file = new UploadCareFile($photoUuid, $photoCdn, $photoName);
            $this->entityManager->persist($file);

            $customer->setPhoto($file);

            $this->entityManager->persist($customer);
            $this->entityManager->flush();
            $this->eventRecorder->registerImageAddEvent($file);

        }
        return new JsonResponse(['status' => 'success']);
    }
}
