<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\Educational;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\SaleFunnel\Educational\Letter;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelEducational;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\UploadCareService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

class RemoveLetterController
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
     * RemoveLetterController constructor.
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
     * @param SalesFunnelEducational $funnel
     * @param Letter                 $letter
     *
     * @return RedirectResponse
     */
    public function handleAction(SalesFunnelEducational $funnel, Letter $letter)
    {
        $this->access->resolveDeleteAccess($funnel);

        if ($letter->getLessonFile()) {
            $this->uploadCareService->removeFile($letter->getLessonFile()->getFileUuid());
            $letter->setLessonFile(null);
        }

        if ($letter->getArticleFile()) {
            $this->uploadCareService->removeFile($letter->getArticleFile()->getFileUuid());
            $letter->setArticleFile(null);
        }

        $funnel->removeLetter($letter);
        $this->entityManager->remove($letter);
        $this->entityManager->persist($funnel);
        $this->entityManager->flush();

        return new RedirectResponse($this->router->generate('hb.sale_funnel.educational.edit', ['id' => $funnel->getId()]));
    }
}