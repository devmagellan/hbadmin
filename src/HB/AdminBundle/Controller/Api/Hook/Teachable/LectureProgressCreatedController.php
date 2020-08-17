<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\Api\Hook\Teachable;

use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Component\Json;
use HB\AdminBundle\Entity\Teachable\Webhook\LectureProgressCreated;
use HB\AdminBundle\Service\TeachableWebhooksMapper;
use Symfony\Component\HttpFoundation\Request;

class LectureProgressCreatedController
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
     * LectureProgressCreatedController constructor.
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

        $webhook = new LectureProgressCreated($content, $referer);
        $this->entityManager->persist($webhook);
        $this->entityManager->flush();

        $this->teachableWebhooksMapper->updateLectureProgress($webhook);

        return Json::ok();
    }
}
