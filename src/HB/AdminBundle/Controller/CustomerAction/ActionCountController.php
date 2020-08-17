<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\CustomerAction;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\ActionLog;
use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Entity\Customer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class ActionCountController
 */
class ActionCountController
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
     * @var Customer
     */
    private $currentUser;

    /**
     * ActionCountController constructor.
     *
     * @param \Twig_Environment      $twig
     * @param EntityManagerInterface $entityManager
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(\Twig_Environment $twig, EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage)
    {
        $this->twig = $twig;
        $this->entityManager = $entityManager;
        $this->currentUser = $tokenStorage->getToken()->getUser();
    }


    /**
     * @return Response
     */
    public function handleAction(): Response
    {
        $actions = $this->getActionLogs();

        $content = $this->twig->render('@HBAdmin/menuActionsCount.html.twig', [
            'menuActionsCount' => \count($actions)
        ]);

        return new Response($content);
    }

    /**
     * @return mixed
     */
    private function getActionLogs()
    {
        $coursesQuery = $this->entityManager->getRepository(Course::class)->getCoursesQuery($this->currentUser);

        $ids = array_map(function($course){
            /** @var Course $course */
            return $course->getId();
        },$coursesQuery->getResult());

        return $this->entityManager->createQueryBuilder()
            ->select('log')
            ->from(ActionLog::class, 'log')
            ->innerJoin('log.course', 'course')
            ->where('log.course in (:ids)')
            ->andWhere('log.published = :false')
            ->andWhere('course.sandbox = :false')
            ->andWhere('log.changeSet != :empty')
            ->setParameter('ids', $ids)
            ->setParameter('false', false)
            ->setParameter('empty', '[]')
            ->getQuery()->getResult();
    }
}