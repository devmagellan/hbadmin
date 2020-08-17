<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\TeachableStudent;


use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr\Join;
use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Entity\Customer;
use HB\AdminBundle\Entity\Teachable\TeachableComment;
use HB\AdminBundle\Entity\Teachable\TeachableTransaction;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CommentController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var Customer
     */
    private $currentUser;

    /**
     * @var PaginatorInterface
     */
    private $paginator;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * CommentController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param TokenStorageInterface  $tokenStorage
     * @param PaginatorInterface     $paginator
     * @param \Twig_Environment      $twig
     */
    public function __construct(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage, PaginatorInterface $paginator, \Twig_Environment $twig)
    {
        $this->entityManager = $entityManager;
        $this->currentUser = $tokenStorage->getToken()->getUser();
        $this->paginator = $paginator;
        $this->twig = $twig;
    }

    public function handleAction(Request $request)
    {
        $filters = $request->get('f', []);
        $studentName = isset($filters['studentName']) ? $filters['studentName'] : null;

        $comments = $this->paginator->paginate($this->getCommentsQuery($studentName), $request->get('page', 1), 50);

        $content = $this->twig->render('@HBAdmin/TeachableStudent/student_comments_list.html.twig', [
            'comments'     => $comments,
            'filters'      => $filters,
            'use_filters'  => empty($filters) ? false : true,
            'studentNames' => $this->getDistinctStudents(),
        ]);

        return new Response($content);

    }

    /**
     * @return array
     */
    private function getDistinctStudents()
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('DISTINCT(com.studentName) as studentName')
            ->from(TeachableComment::class, 'com')
            ->innerJoin(Course::class, 'course', Join::WITH, 'com.course_id = course.teachableId')
            ->orderBy('com.studentName', 'ASC');

        if (!$this->currentUser->isAdmin()) {
            $qb->where('course.id in (:ids)')
                ->setParameter('ids', $this->getCoursesIds());
        }

        $result = [];

        foreach ($qb->getQuery()->getScalarResult() as $studentName) {
            $result[$studentName['studentName']] = $studentName['studentName'];
        }

        return $result;
    }

    private function getCommentsQuery(string $studentName = null): Query
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('com')
            ->from(TeachableComment::class, 'com')
            ->innerJoin(Course::class, 'course', Join::WITH, 'com.course_id = course.teachableId')
            ->orderBy('com.createdAt', 'DESC');


        if (!$this->currentUser->isAdmin()) {
            $qb->where('course.id in (:ids)')
                ->setParameter('ids', $this->getCoursesIds());
        }

        if ($studentName) {
            $qb->andWhere('com.studentName = :studentName')->setParameter('studentName', $studentName);
        }

        return $qb->getQuery();
    }

    /**
     * @return array
     */
    private function getCoursesIds()
    {
        $courses = $this->entityManager->getRepository(Course::class)->getCoursesQuery($this->currentUser);

        $ids = array_map(function ($course) {
            /** @var Course $course */
            return $course->getId();
        }, $courses->getResult());

        return $ids;
    }


}