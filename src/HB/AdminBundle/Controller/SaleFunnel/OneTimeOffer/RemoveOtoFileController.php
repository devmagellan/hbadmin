<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\OneTimeOffer;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelOneTimeOffer;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\UploadCareService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RemoveOtoFileController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var UploadCareService
     */
    private $uploadCareService;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var CourseAccessService
     */
    private $access;

    /**
     * RemoveOtoFileController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param UploadCareService      $uploadCareService
     * @param UrlGeneratorInterface  $urlGenerator
     * @param CourseAccessService    $access
     */
    public function __construct(EntityManagerInterface $entityManager, UploadCareService $uploadCareService, UrlGeneratorInterface $urlGenerator, CourseAccessService $access)
    {
        $this->entityManager = $entityManager;
        $this->uploadCareService = $uploadCareService;
        $this->urlGenerator = $urlGenerator;
        $this->access = $access;
    }


    /**
     * @param SalesFunnelOneTimeOffer $funnel
     * @param string                  $type
     * @param Request                 $request
     *
     * @return RedirectResponse
     */
    public function handleAction(SalesFunnelOneTimeOffer $funnel, string $type, Request $request)
    {
        $this->access->resolveDeleteAccess($funnel);

        $previousFile = null;


        if (SalesFunnelOneTimeOffer::OTO_FILE_TYPE_OFFER === $type) {
            $previousFile = $funnel->getOfferFile();
            $funnel->setOfferFile(null);
        } else if (SalesFunnelOneTimeOffer::OTO_FILE_TYPE_IMAGE === $type) {
            $previousFile = $funnel->getImage();
            $funnel->setImage(null);
        } else if (SalesFunnelOneTimeOffer::OTO_FILE_TYPE_VIDEO === $type) {
            $previousFile = $funnel->getVideo();
            $funnel->setVideo(null);
        }

        if ($previousFile) {
            $this->uploadCareService->removeFile($previousFile->getFileUuid());
            $this->entityManager->remove($previousFile);
        }

        $this->entityManager->persist($funnel);
        $this->entityManager->flush();


        $url = $request->headers->get('referer', $this->urlGenerator->generate('hb.sale_funnel.one_time_offer.edit', ['id' => $funnel->getId()]));

        return new RedirectResponse($url);
    }
}
