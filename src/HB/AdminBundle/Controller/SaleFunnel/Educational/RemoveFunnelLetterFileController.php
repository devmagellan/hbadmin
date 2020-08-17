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

class RemoveFunnelLetterFileController
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
     * @param Letter                 $letter
     * @param string                 $type
     *
     * @return RedirectResponse
     */
    public function handleAction(SalesFunnelEducational $funnel, Letter $letter, string $type)
    {
        if ($funnel->getLetters()->contains($letter)) {
            $this->access->resolveDeleteAccess($funnel);
            $file = null;

            if (Letter::ARTICLE_FILE === $type) {
                $file = $letter->getArticleFile();
                $letter->setArticleFile(null);
            } elseif (Letter::LESSON_FILE === $type) {
                $file = $letter->getLessonFile();
                $letter->setLessonFile(null);
            }

            if ($file) {
                $this->uploadCareService->removeFile($file->getFileUuid());
                $this->entityManager->remove($file);

                $this->entityManager->persist($letter);
                $this->entityManager->flush();
            }
        }

        return new RedirectResponse($this->router->generate('hb.sale_funnel.educational.edit', ['id' => $funnel->getId()]));
    }
}