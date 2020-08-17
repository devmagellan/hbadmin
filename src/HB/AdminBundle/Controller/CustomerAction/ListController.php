<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\CustomerAction;


use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr;
use HB\AdminBundle\Entity\ActionLog;
use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Entity\Customer;
use HB\AdminBundle\Service\CustomerAccessService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class ListController
 */
class ListController
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
     * @var PaginatorInterface
     */
    private $paginator;

    /**
     * @var Customer
     */
    private $currentUser;

    /**
     * @var CustomerAccessService
     */
    private $customerAccessService;

    /**
     * @var array
     */
    private $coursesIds = [];

    /**
     * @var array
     */
    private $customerIds = [];

    /**
     * ListController constructor.
     *
     * @param \Twig_Environment      $twig
     * @param EntityManagerInterface $entityManager
     * @param PaginatorInterface     $paginator
     * @param TokenStorageInterface  $tokenStorage
     * @param CustomerAccessService  $customerAccessService
     */
    public function __construct(\Twig_Environment $twig, EntityManagerInterface $entityManager, PaginatorInterface $paginator, TokenStorageInterface $tokenStorage, CustomerAccessService $customerAccessService)
    {
        $this->twig = $twig;
        $this->entityManager = $entityManager;
        $this->paginator = $paginator;
        $this->currentUser = $tokenStorage->getToken()->getUser();
        $this->customerAccessService = $customerAccessService;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function handleAction(Request $request): Response
    {
        $filters = $request->get('f', []);
        $page = $request->get('page', 1);

        $logs = $this->paginator->paginate($this->getLogsQuery($filters), $page, 50);

        $coursesWithLogs = $this->getCoursesWithLogs();
        $editors = $this->getCourseEditors();

        $content = $this->twig->render('@HBAdmin/CustomerActions/index.html.twig', [
            'logs'           => $logs,
            'filter_courses' => $coursesWithLogs,
            'filter_editors' => $editors,
            'filters'        => $filters,
            'use_filters'    => empty($filters) ? false : true,
        ]);

        return new Response($content);
    }

    /**
     * @param array $filters
     *
     * @return \Doctrine\ORM\Query
     */
    private function getLogsQuery(array $filters)
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('log')
            ->from(ActionLog::class, 'log')
            ->andWhere('log.changeSet != :empty')
            ->andWhere('log.course in (:ids)')
            ->setParameter('ids', $this->getAvailableCoursesIds())
            ->setParameter('empty', '[]');

        if ((array_key_exists('published', $filters) && $filters['published'] === '') || !array_key_exists('published', $filters)) {
            $query->andWhere('log.published = :published')
                ->setParameter('published', false);
        }

        if (array_key_exists('course', $filters) && $filters['course'] !== '') {
            $query->andWhere('log.course = :course')
                ->setParameter('course', $filters['course']);
        }

        if (array_key_exists('editor', $filters) && $filters['editor'] !== '') {
            $query->andWhere('log.user = :editor')
                ->setParameter('editor', $filters['editor']);
        }

        return $query->orderBy('log.id', 'DESC')
            ->getQuery();
    }

    /**
     * Return courses with changes
     *
     * @return array | Course[]
     */
    private function getCoursesWithLogs()
    {
        return $this->entityManager->createQueryBuilder()
            ->select('course')
            ->from(Course::class, 'course')
            ->innerJoin(ActionLog::class, 'log', Expr\Join::WITH, 'course.id = log.course')
            ->andWhere('course.sandbox = :false')
            ->andWhere('course.id in (:ids)')
            ->setParameter('false', false)
            ->setParameter('ids', $this->getAvailableCoursesIds())
            ->orderBy('course.info.title', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Return customers who made changes
     *
     * @return array | Customer[]
     */
    private function getCourseEditors()
    {
        return $this->entityManager->createQueryBuilder()
            ->select('customer')
            ->from(Customer::class, 'customer')
            ->innerJoin(ActionLog::class, 'log', Expr\Join::WITH, 'customer.id = log.user')
            ->where('customer.id in (:ids)')
            ->setParameter('ids', $this->getAvailableCustomersIds())
            ->getQuery()->getResult();
    }

    /**
     * @return array
     */
    private function getAvailableCoursesIds()
    {
        if (empty($this->coursesIds)) {

            $coursesQuery = $this->entityManager->getRepository(Course::class)->getCoursesQuery($this->currentUser);
            $courses = $coursesQuery->getResult();

            $ids = array_map(function ($course) {
                /** @var Course $course */
                return $course->getId();
            }, $courses);
            $this->coursesIds = $ids;
        } else {
            $ids = $this->coursesIds;
        }

        return $ids;
    }

    /**
     * @return array
     */
    private function getAvailableCustomersIds()
    {
        if (!empty($this->customerIds)) {
            $ids = $this->customerIds;
        } else {
            $query = $this->customerAccessService->getCustomersListQuery();
            $customers = $query->getResult();

            $ids = array_map(function ($customer) {
                /** @var Customer $customer */
                return $customer->getId();
            }, $customers);
            $this->customerIds = $ids;
        }
        return $ids;
    }
}