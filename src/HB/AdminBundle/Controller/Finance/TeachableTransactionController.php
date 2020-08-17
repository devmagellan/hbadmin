<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\Finance;


use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Entity\Customer;
use HB\AdminBundle\Entity\Teachable\TeachableTransaction;
use HB\AdminBundle\Helper\ArrayHelper;
use HB\AdminBundle\Service\CustomerAccessService;
use HB\AdminBundle\Service\DqlFilters;
use HB\AdminBundle\Service\StatisticCollector;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TeachableTransactionController
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
     * @var PaginatorInterface
     */
    private $paginator;

    /**
     * @var Customer
     */
    private $currentUser;

    /**
     * @var StatisticCollector
     */
    private $statisticCollector;

    /**
     * @var CustomerAccessService
     */
    private $customerAccessService;

    /**
     * TeachableTransactionController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param \Twig_Environment      $twig
     * @param PaginatorInterface     $paginator
     * @param TokenStorageInterface  $tokenStorage
     * @param StatisticCollector     $statisticCollector
     * @param CustomerAccessService  $customerAccessService
     */
    public function __construct(EntityManagerInterface $entityManager, \Twig_Environment $twig, PaginatorInterface $paginator, TokenStorageInterface $tokenStorage, StatisticCollector $statisticCollector, CustomerAccessService $customerAccessService)
    {
        $this->entityManager = $entityManager;
        $this->twig = $twig;
        $this->paginator = $paginator;
        $this->currentUser = $tokenStorage->getToken()->getUser();
        $this->statisticCollector = $statisticCollector;
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
        $from = isset($filters['startDate']) && $filters['startDate'] !== ''
            ? \DateTime::createFromFormat('Y-m-d', $filters['startDate'])->setTime(0,0,0)
            : null;
        $to = isset($filters['endDate']) && $filters['endDate'] !== ''
            ? \DateTime::createFromFormat('Y-m-d', $filters['endDate'])->setTime(23,59,59)
            : null;

        $repository = $this->entityManager->getRepository(TeachableTransaction::class);

        $availableCustomerIds = $this->customerAccessService->getAvailableCustomerIds();

        $coursesIds = $this->getCoursesIds();

        $transactionsQuery = $this->createQuery($filters, $coursesIds, $this->currentUser->isAdmin(), $from, $to);

        /** @var TeachableTransaction[] $transactions */
        $transactions = $this->paginator->paginate($transactionsQuery, $request->get('page', 1), 50);

        $statistic = $this->statisticCollector->getStatistic(
            $coursesIds,
            $this->currentUser->isAdmin(),
            isset($filters['affiliate']) ? $filters['affiliate'] : null,
            isset($filters['plan']) ? $filters['plan'] : null,
            isset($filters['author']) ? (int) $filters['author'] : null,
            isset($filters['courseName']) ? $filters['courseName'] : null,
            $availableCustomerIds,
            $from,
            $to
        );

        $content = $this->twig->render('@HBAdmin/Finance/transaction_list.html.twig', [
            'transactions' => $transactions,
            'use_filters'  => empty($filters) ? false : true,
            'filters'      => $filters,
            'courses'      => $repository->getDistinctCourses($coursesIds, $this->currentUser->isAdmin()),
            'affiliates'   => $repository->getDistinctAffiliates($coursesIds, $this->currentUser->isAdmin()),
            'plans'        => $repository->getDistinctPlans($coursesIds, $this->currentUser->isAdmin()),
            'authors'      => $this->getAuthorsChoices($availableCustomerIds),
            'statistic'    => $statistic,
        ]);

        return new Response($content);
    }

    /**
     * @param array            $filters
     * @param array            $coursesIds
     * @param bool             $isAdmin
     * @param \DateTime | null $from
     * @param \DateTime | null $to
     *
     * @return Query
     */
    public function createQuery(array $filters, array $coursesIds, bool $isAdmin, \DateTime $from = null, \DateTime $to = null): Query
    {
        $filters['from'] = $from;
        $filters['to'] = $to;

        $filters = new DqlFilters($filters);

        $filters
            ->equal('t.course_name', 'courseName')
            ->equal('t.affiliateName', 'affiliate')
            ->equal('t.product_plan', 'plan')
            ->equal('author.id', 'author')
            ->greaterOrEqual('t.createdAt', 'from')
            ->lessOrEqual('t.createdAt', 'to')
        ;

        if (!$isAdmin) {
            $filters->in('t.internalCourse', $coursesIds);
        }

        $dql = "SELECT t  FROM HBAdminBundle:Teachable\TeachableTransaction t
                LEFT JOIN t.internalCourse course
                LEFT JOIN course.owner author
                ".$filters->getDql()." ORDER BY t.createdAt DESC";

        return $this->entityManager->createQuery($dql)->setParameters($filters->getParameters());
    }


    /**
     * @return array
     */
    private function getAuthorsChoices(array $availableCustomerIds)
    {
        $result = [];

        $ids = ArrayHelper::getArrayByIndex($this->entityManager->getRepository(TeachableTransaction::class)->getDistinctAuthorsIds(), 'id');
        $authors = $this->entityManager->getRepository(Customer::class)->getCustomersByIds($ids);

        /** @var Customer $author */
        foreach ($authors as $author) {
            if (in_array($author->getId(), $availableCustomerIds)) {
                $result[$author->getId()] = $author->__toString();
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    private function getCoursesIds(): array
    {
        $coursesQuery = $this->entityManager->getRepository(Course::class)->getCoursesQuery($this->currentUser);
        $courses = $coursesQuery->getResult();

        $ids = array_map(function ($course) {
            /** @var Course $course */
            return $course->getId();
        }, $courses);

        return $ids;
    }


}