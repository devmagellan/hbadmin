<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\Api;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Component\Json;
use HB\AdminBundle\Controller\Api\Resource\SignUpResource;
use HB\AdminBundle\Entity\Customer;
use HB\AdminBundle\Entity\CustomerRole;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SignUpController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * SignUpController constructor.
     *
     * @param EntityManagerInterface       $entityManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
    }


    /**
     * Request type: POST, Body: json raw.
     *
     * Required:
     * * email
     * * password
     * * name
     * * hash
     *
     * @param SignUpResource $signUpResource
     *
     * @return JsonResponse
     */
    public function handleAction(SignUpResource $signUpResource)
    {
        $emailExist = $this->entityManager->getRepository(Customer::class)->findOneBy(['email' => $signUpResource->email]);

        if ($emailExist) {
            return Json::error('Such email already exist');
        }

        $customer = new Customer();
        $password = $this->passwordEncoder->encodePassword($customer, $signUpResource->password);
        $customer->setPassword($password);

        $customer->setEmail($signUpResource->email);
        $customer->setRole($this->getAuthorRole());

        $this->entityManager->persist($customer);
        $this->entityManager->flush();

        return Json::ok('Customer was created.');

    }

    /**
     * @return CustomerRole
     */
    private function getAuthorRole(): CustomerRole
    {
        return $this->entityManager->getRepository(CustomerRole::class)->findOneBy(['name' => CustomerRole::ROLE_AUTHOR]);
    }
}