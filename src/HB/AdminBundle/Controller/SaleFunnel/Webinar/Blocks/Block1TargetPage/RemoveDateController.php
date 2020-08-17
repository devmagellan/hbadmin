<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\Webinar\Blocks\Block1TargetPage;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Component\Json;
use HB\AdminBundle\Entity\SaleFunnel\Webinar\WebinarDate;
use Symfony\Component\HttpFoundation\JsonResponse;

class RemoveDateController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * RemoveDateController constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param WebinarDate $webinarDate
     *
     * @return JsonResponse
     */
    public function handleAction(WebinarDate $webinarDate)
    {
        $this->entityManager->remove($webinarDate);
        $this->entityManager->flush();

        return Json::ok();
    }
}