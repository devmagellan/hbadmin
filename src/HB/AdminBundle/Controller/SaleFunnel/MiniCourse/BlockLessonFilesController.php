<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\MiniCourse;


use HB\AdminBundle\Entity\SaleFunnel\MiniCourse\MiniLesson;
use Symfony\Component\HttpFoundation\Response;

class BlockLessonFilesController
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * BlockLessonFilesController constructor.
     *
     * @param \Twig_Environment $twig
     */
    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @param MiniLesson $lesson
     *
     * @return Response
     */
    public function handleAction(MiniLesson $lesson)
    {
        $content = $this->twig->render('@HBAdmin/SaleFunnel/MiniCourse/mini_course_files_block.html.twig', [
            'lesson' => $lesson
        ]);

        return new Response($content);
    }
}