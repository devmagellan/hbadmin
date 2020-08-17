<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\TeachableStudent;


use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Entity\Customer;
use HB\AdminBundle\Entity\Teachable\TeachableComment;
use HB\AdminBundle\Entity\Teachable\TeachableLectureProgress;
use HB\AdminBundle\Entity\Teachable\TeachableQuizResponse;
use HB\AdminBundle\Entity\Teachable\TeachableTransaction;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ProgressListController
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
     * ListController constructor.
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

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function handleAction(Request $request)
    {
        $filters = $request->get('f', []);

        $courseName = isset($filters['courseName']) ? $filters['courseName'] : null;
        $studentName = isset($filters['$studentName']) ? $filters['$studentName'] : null;

        $progress = $this->paginator->paginate($this->getLectureProgressQuery($courseName, $studentName), $request->get('page', 1), 50);

        $content = $this->twig->render('@HBAdmin/TeachableStudent/student_progress_list.html.twig', [
            'students'     => $progress,
            'courseNames'  => $this->getDistinctCoursesNames(),
            'studentNames' => $this->getDistinctStudents(),
            'filters'      => $filters,
            'use_filters'  => empty($filters) ? false : true,
        ]);

        return new Response($content);
    }

    /**
     * @param string|null $courseName
     * @param string|null $studentName
     *
     * @return \Doctrine\ORM\Query
     */
    private function getLectureProgressQuery(string $courseName = null, string $studentName = null)
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('p as progress, COUNT(DISTINCT(q.id)) as quizCount, COUNT(DISTINCT(com.id)) as commentsCount')
            ->from(TeachableLectureProgress::class, 'p')
            ->leftJoin(TeachableQuizResponse::class, 'q', Join::WITH, 'p.studentEmail = q.studentEmail AND p.course_id = q.course_id')
            ->leftJoin(TeachableComment::class, 'com', Join::WITH, 'p.studentEmail = com.studentEmail AND p.course_id = com.course_id')
            ->innerJoin(Course::class, 'course', Join::WITH, 'p.course_id = course.teachableId')
            ->groupBy('p.id')
            ->orderBy('p.studentName');

        if (!$this->currentUser->isAdmin()) {
            $qb->where('course.id in (:ids)')
                ->setParameter('ids', $this->getCoursesIds());
        }
        if ($courseName) {
            $qb->andWhere('p.courseName = :courseName')->setParameter('courseName', $courseName);
        }

        if ($studentName) {
            $qb->andWhere('p.studentName = :studentName')->setParameter('studentName', $studentName);
        }

        return $qb->getQuery();
    }

    /**
     * @return array
     */
    private function getDistinctCoursesNames()
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('DISTINCT(p.courseName) as courseName')
            ->from(TeachableLectureProgress::class, 'p')
            ->innerJoin(Course::class, 'course', Join::WITH, 'p.course_id = course.teachableId')
            ->orderBy('p.courseName', 'ASC');

        if (!$this->currentUser->isAdmin()) {
            $qb->where('course.id in (:ids)')
                ->setParameter('ids', $this->getCoursesIds());
        }

        $result = [];

        foreach ($qb->getQuery()->getScalarResult() as $courseName) {
            $result[$courseName['courseName']] = $courseName['courseName'];
        }

        return $result;
    }

    /**
     * @return array
     */
    private function getDistinctStudents()
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('DISTINCT(p.studentName) as studentName')
            ->from(TeachableLectureProgress::class, 'p')
            ->innerJoin(Course::class, 'course', Join::WITH, 'p.course_id = course.teachableId')
            ->orderBy('p.studentName', 'ASC');

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