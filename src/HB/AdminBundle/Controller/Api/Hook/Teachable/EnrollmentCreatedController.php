<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\Api\Hook\Teachable;

use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Component\Json;
use HB\AdminBundle\Entity\Teachable\Webhook\EnrollmentCreated;
use HB\AdminBundle\Service\TeachableWebhooksMapper;
use Symfony\Component\HttpFoundation\Request;

class EnrollmentCreatedController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var TeachableWebhooksMapper
     */
    private $teachableWebhooksMapper;

    /**
     * EnrollmentCreatedController constructor.
     *
     * @param EntityManagerInterface  $entityManager
     * @param TeachableWebhooksMapper $teachableWebhooksMapper
     */
    public function __construct(EntityManagerInterface $entityManager, TeachableWebhooksMapper $teachableWebhooksMapper)
    {
        $this->entityManager = $entityManager;
        $this->teachableWebhooksMapper = $teachableWebhooksMapper;
    }

    /**
     * @param Request $request
     */
    public function handleAction(Request $request)
    {
        $content = $request->getContent();
        $referer = $request->headers->get('referer');

        $webhook = new EnrollmentCreated($content, $referer);
        $this->entityManager->persist($webhook);
        $this->entityManager->flush();

        $this->teachableWebhooksMapper->updateEnrollment($webhook);

        return Json::ok();
    }
}
