<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\Organic;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\Lesson;
use HB\AdminBundle\Entity\LessonSection;
use Symfony\Component\HttpFoundation\Response;

class PreviewSectionController
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
     * PreviewSectionController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param \Twig_Environment      $twig
     */
    public function __construct (EntityManagerInterface $entityManager, \Twig_Environment $twig)
    {
        $this->entityManager = $entityManager;
        $this->twig = $twig;
    }

    /**
     * @param LessonSection $section
     * @param boolean       $first
     */
    public function handleAction (LessonSection $section, bool $first = false)
    {
        $lessons = $this->entityManager->getRepository(Lesson::class)->findBy(['section' => $section], ['priority' => 'ASC']);

        $content = $this->twig->render('@HBAdmin/SaleFunnel/Organic/preview_section.html.twig', [
            'lessons' => $lessons,
            'first'   => $first,
            'section' => $section,
        ]);

        return new Response($content);
    }
}