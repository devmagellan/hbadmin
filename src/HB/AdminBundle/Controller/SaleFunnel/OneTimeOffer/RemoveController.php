<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\OneTimeOffer;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelOneTimeOffer;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\UploadCareService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

class RemoveController
{
    /**
     * @var UploadCareService
     */
    private $uploadCareService;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var CourseAccessService
     */
    private $access;

    /**
     * RemoveController constructor.
     *
     * @param UploadCareService      $uploadCareService
     * @param EntityManagerInterface $entityManager
     * @param RouterInterface        $router
     * @param CourseAccessService    $access
     */
    public function __construct(UploadCareService $uploadCareService, EntityManagerInterface $entityManager, RouterInterface $router, CourseAccessService $access)
    {
        $this->uploadCareService = $uploadCareService;
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->access = $access;
    }


    /**
     * @param SalesFunnelOneTimeOffer $funnel
     *
     * @return RedirectResponse
     */
    public function handleAction(SalesFunnelOneTimeOffer $funnel)
    {
        $this->access->resolveDeleteAccess($funnel);

        $courseId = $funnel->getCourse()->getId();

        $this->removeFunnelFiles($funnel);

        $this->entityManager->remove($funnel);
        $this->entityManager->flush();

        return new RedirectResponse($this->router->generate('hb.sale_funnel.one_time_offer.list', ['id' => $courseId]));
    }

    /**
     * @param SalesFunnelOneTimeOffer $funnel
     */
    private function removeFunnelFiles(SalesFunnelOneTimeOffer $funnel)
    {
        if ($funnel->getImage()) {
            $this->uploadCareService->removeFile($funnel->getImage()->getFileUuid());
            $this->entityManager->remove($funnel->getImage());
            $funnel->setImage(null);

        }

        if ($funnel->getVideo()) {
            $this->uploadCareService->removeFile($funnel->getVideo()->getFileUuid());
            $this->entityManager->remove($funnel->getVideo());
            $funnel->setVideo(null);
        }

        if ($funnel->getOfferFile()) {
            $this->uploadCareService->removeFile($funnel->getOfferFile()->getFileUuid());
            $this->entityManager->remove($funnel->getOfferFile());
            $funnel->setOfferFile(null);
        }
    }

}