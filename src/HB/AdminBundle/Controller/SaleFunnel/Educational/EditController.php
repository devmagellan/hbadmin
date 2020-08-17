<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\Educational;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\SaleFunnel\Educational\Letter;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelEducational;
use HB\AdminBundle\Entity\UploadCareFile;
use HB\AdminBundle\Form\SaleFunnel\Educational\LetterType;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\FormHandler;
use HB\AdminBundle\Service\Intercom\IntercomEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EditController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var FormHandler
     */
    private $formHandler;

    /**
     * @var CourseAccessService
     */
    private $access;

    /**
     * EditController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param \Twig_Environment      $twig
     * @param FormFactoryInterface   $formFactory
     * @param FormHandler            $formHandler
     * @param CourseAccessService    $access
     */
    public function __construct(EntityManagerInterface $entityManager, \Twig_Environment $twig, FormFactoryInterface $formFactory, FormHandler $formHandler, CourseAccessService $access)
    {
        $this->entityManager = $entityManager;
        $this->twig = $twig;
        $this->formFactory = $formFactory;
        $this->formHandler = $formHandler;
        $this->access = $access;
    }


    /**
     * @param SalesFunnelEducational $funnel
     * @param Request                $request
     *
     * @return Response
     */
    public function handleAction(SalesFunnelEducational $funnel, Request $request)
    {
        $this->access->resolveUpdateAccess($funnel);
        $this->access->registerEvent(IntercomEvents::ACCESS_FUNNEL_EDUCATIONAL);

        $letter = new Letter();
        $form = $this->formFactory->create(LetterType::class, $letter);
        $added = false;

        if ($this->formHandler->handle($letter, $request, $form)) {
            $funnel->addLetter($letter);
            $this->entityManager->persist($funnel);
            $this->entityManager->flush();

            $lessonFile = $request->get('letter_lesson_file');
            $articleFile = $request->get('letter_article_file');

            if ($lessonFile) {
                $lessonFile = $this->entityManager->getRepository(UploadCareFile::class)->find((int) $lessonFile);
                $letter->setLessonFile($lessonFile);
            }

            if ($articleFile) {
                $articleFile = $this->entityManager->getRepository(UploadCareFile::class)->find((int) $articleFile);
                $letter->setArticleFile($articleFile);
            }

            $this->entityManager->persist($letter);
            $this->entityManager->flush();
            $added = true;
        }

        $content = $this->twig->render('@HBAdmin/SaleFunnel/Educational/edit.html.twig', [
            'funnel' => $funnel,
            'form'   => $form->createView(),
            'added'  => $added,
        ]);

        return new Response($content);
    }

}