<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\Finance\Report\Admin;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\CustomerPaymentReport;
use HB\AdminBundle\Service\UploadCareService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * @IsGranted("ROLE_ADMIN")
 */
class ReportRemoveController
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
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * ReportRemoveController constructor.
     *
     * @param UploadCareService      $uploadCareService
     * @param EntityManagerInterface $entityManager
     * @param RouterInterface        $router
     * @param FlashBagInterface      $flashBag
     */
    public function __construct(UploadCareService $uploadCareService, EntityManagerInterface $entityManager, RouterInterface $router, FlashBagInterface $flashBag)
    {
        $this->uploadCareService = $uploadCareService;
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->flashBag = $flashBag;
    }

    /**
     * @param CustomerPaymentReport $report
     *
     * @return RedirectResponse
     */
    public function handleAction(CustomerPaymentReport $report)
    {
        $this->entityManager->remove($report);


        if ($report->getFile()) {
            $this->entityManager->remove($report->getFile());
            $this->uploadCareService->removeFile($report->getFile()->getFileUuid());
        }

        $this->entityManager->flush();
        $this->flashBag->add('success', 'Отчет удален');

        return new RedirectResponse($this->router->generate('hb.finance.admin.reports'));
    }

}