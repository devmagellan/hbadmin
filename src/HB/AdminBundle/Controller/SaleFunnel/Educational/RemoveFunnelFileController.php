<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\Educational;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelEducational;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\UploadCareService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

class RemoveFunnelFileController
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
     * @var CourseAccessService
     */
    private $access;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * RemoveFunnelFileController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param UploadCareService      $uploadCareService
     * @param CourseAccessService    $access
     * @param RouterInterface        $router
     */
    public function __construct(EntityManagerInterface $entityManager, UploadCareService $uploadCareService, CourseAccessService $access, RouterInterface $router)
    {
        $this->entityManager = $entityManager;
        $this->uploadCareService = $uploadCareService;
        $this->access = $access;
        $this->router = $router;
    }

    /**
     * @param SalesFunnelEducational $funnel
     * @param string                 $type
     *
     * @return RedirectResponse
     */
    public function handleAction(SalesFunnelEducational $funnel, string $type)
    {
        $this->access->resolveDeleteAccess($funnel);

        if ($type) {

            $file = null;

            if (SalesFunnelEducational::ARTICLES_FILE === $type) {

                $file = $funnel->getArticlesFile();
                $funnel->setArticlesFile(null);
            } else if (SalesFunnelEducational::LETTERS_FILE === $type) {

                $file = $funnel->getLettersFile();
                $funnel->setLettersFile(null);
            }

            if ($file) {
                $this->uploadCareService->removeFile($file->getFileUuid());
                $this->entityManager->remove($file);
            }

            $this->entityManager->persist($funnel);
            $this->entityManager->flush();
        }

        return new RedirectResponse($this->router->generate('hb.sale_funnel.educational.edit', ['id' => $funnel->getId()]));
    }
}