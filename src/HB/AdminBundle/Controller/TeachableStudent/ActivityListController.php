<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\TeachableStudent;


use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Entity\Customer;
use HB\AdminBundle\Entity\Teachable\TeachableCourseStudent;
use HB\AdminBundle\Entity\Teachable\TeachableTransaction;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ActivityListController
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
        $fromLastLogin = isset($filters['startDateLastLogin']) && $filters['startDateLastLogin'] !== ''
            ? \DateTime::createFromFormat('Y-m-d', $filters['startDateLastLogin'])->setTime(0, 0, 0)
            : null;
        $toLastLogin = isset($filters['endDateLastLogin']) && $filters['endDateLastLogin'] !== ''
            ? \DateTime::createFromFormat('Y-m-d', $filters['endDateLastLogin'])->setTime(23, 59, 59)
            : null;

        $fromJoinedAt = isset($filters['startDateJoinedAt']) && $filters['startDateJoinedAt'] !== ''
            ? \DateTime::createFromFormat('Y-m-d', $filters['startDateJoinedAt'])->setTime(0, 0, 0)
            : null;
        $toJoinedAt = isset($filters['endDateJoinedAt']) && $filters['endDateJoinedAt'] !== ''
            ? \DateTime::createFromFormat('Y-m-d', $filters['endDateJoinedAt'])->setTime(23, 59, 59)
            : null;

        $courseName = isset($filters['courseName']) ? $filters['courseName'] : null;

        $students = $this->paginator->paginate($this->getStudentsQuery($fromLastLogin, $toLastLogin, $fromJoinedAt, $toJoinedAt, $courseName), $request->get('page', 1), 50);

        $content = $this->twig->render('@HBAdmin/TeachableStudent/student_activity_list.html.twig', [
            'students'    => $students,
            'courseNames' => $this->getDistinctCourses(),
            'filters'     => $filters,
            'use_filters'  => empty($filters) ? false : true,
        ]);

        return new Response($content);
    }


    private function getStudentsQuery(\DateTime $fromLastLogin = null, \DateTime $toLastLogin = null, \DateTime $fromJoinedAt = null, \DateTime $toJoinedAt = null, string $courseName = null)
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('s as student, SUM(tr.finalPrice) as salesSum')
            ->from(TeachableCourseStudent::class, 's')
            ->leftJoin(TeachableTransaction::class, 'tr', Join::WITH, 's.id = tr.teachableStudent')
            ->innerJoin('tr.internalCourse', 'internal_course')
            ->where('tr.status = :paid')
            ->setParameter('paid', TeachableTransaction::TRANSACTION_STATUS_PAID)
            ->groupBy('s.id')
            ->orderBy('s.studentName', 'ASC');

        if ($fromLastLogin) {
            $qb->andWhere('s.lastSignInAt >= :fromLastLogin')->setParameter('fromLastLogin', $fromLastLogin);
        }

        if ($toLastLogin) {
            $qb->andWhere('s.lastSignInAt <= :toLastLogin')->setParameter('toLastLogin', $toLastLogin);
        }

        if ($fromJoinedAt) {
            $qb->andWhere('s.lastActivityAt >= :fromJoinedAt')->setParameter('fromJoinedAt', $fromJoinedAt);
        }

        if ($toJoinedAt) {
            $qb->andWhere('s.lastActivityAt <= :toJoinedAt')->setParameter('toJoinedAt', $toJoinedAt);
        }

        if ($courseName) {
            $qb->andWhere('s.courseName = :courseName')->setParameter('courseName', $courseName);
        }

        if (!$this->currentUser->isAdmin()) {
            $coursesIds = $this->getCoursesIds();

            $qb->andWhere('internal_course.id in (:ids)')
                ->setParameter('ids', $coursesIds);
        }

        return $qb->getQuery();
    }

    /**
     * @return array
     */
    private function getDistinctCourses()
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('DISTINCT(s.courseName) as courseName')
            ->from(TeachableCourseStudent::class, 's')
            ->leftJoin(TeachableTransaction::class, 'tr', Join::WITH, 's.id = tr.teachableStudent')
            ->innerJoin('tr.internalCourse', 'internal_course')
            ->where('tr.status = :paid')
            ->setParameter('paid', TeachableTransaction::TRANSACTION_STATUS_PAID)
            ->orderBy('s.courseName', 'ASC');

        if (!$this->currentUser->isAdmin()) {
            $coursesIds = $this->getCoursesIds();

            $qb->andWhere('internal_course.id in (:ids)')
                ->setParameter('ids', $coursesIds);
        }

        $result = [];
        /** @var TeachableCourseStudent $courseName */
        foreach ($qb->getQuery()->getScalarResult() as $courseName) {
            $result[$courseName['courseName']] = $courseName['courseName'];
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