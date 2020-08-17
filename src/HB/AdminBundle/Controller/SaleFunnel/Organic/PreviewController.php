<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\Organic;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Entity\LessonSection;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelOrganic;
use Symfony\Component\HttpFoundation\Response;

class PreviewController
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * PreviewController constructor.
     *
     * @param \Twig_Environment      $twig
     * @param EntityManagerInterface $entityManager
     */
    public function __construct (\Twig_Environment $twig, EntityManagerInterface $entityManager)
    {
        $this->twig = $twig;
        $this->entityManager = $entityManager;
    }


    /**
     * @param SalesFunnelOrganic $funnel
     *
     * @return Response
     */
    public function handleAction (SalesFunnelOrganic $funnel)
    {
        $sections = $this->entityManager->getRepository(LessonSection::class)->findBy(['course' => $funnel->getCourse()], ['priority' => 'ASC']);

        $content = $this->twig->render('@HBAdmin/SaleFunnel/Organic/preview.html.twig', [
            'funnel'   => $funnel,
            'sections' => $sections,
            'lessonsCount' => $this->getLessonsCount($funnel->getCourse())
        ]);

        return new Response($content);
    }

    /**
     * @param Course $course
     *
     * @return int
     */
    private function getLessonsCount(Course $course)
    {
        return (int) $this->entityManager->createQueryBuilder()->
        select('COUNT(lesson)')
            ->from(LessonSection::class, 'lesson_section')
            ->leftJoin('lesson_section.lessons', 'lesson')
            ->leftJoin('lesson_section.priceBlocks', 'price_block')
            ->where('lesson_section.course = :course')
            ->setParameters([
                'course' => $course,
            ])
            ->getQuery()->getSingleScalarResult();
    }
}