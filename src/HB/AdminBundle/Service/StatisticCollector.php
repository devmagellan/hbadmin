<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Service;


use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use HB\AdminBundle\DTO\StatsDTO;
use HB\AdminBundle\Entity\CustomerPayment;
use HB\AdminBundle\Entity\Teachable\TeachableTransaction;

class StatisticCollector
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * StatisticCollector constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param array          $coursesIds
     * @param bool           $isAdmin
     * @param string|null    $partner
     * @param string|null    $plan
     * @param int|null       $authorId
     * @param string|null    $course
     * @param array          $customerIds
     * @param \DateTime|null $from
     * @param \DateTime|null $to
     *
     * @return StatsDTO
     */
    public function getStatistic(array $coursesIds = [], bool $isAdmin = false, string $partner = null, string $plan = null, int $authorId = null, string $course = null, array $customerIds = [], \DateTime $from = null, \DateTime $to = null): StatsDTO
    {
        $select = 'SUM(t.finalPrice - t.refundAmount) as sales, SUM(t.income) as income';
        $incomeStatsQueryBuilder = $this->applyFilters(
            $this->prepareQueryBuilder($select),
            $coursesIds,
            $partner,
            $isAdmin,
            $plan,
            $authorId,
            $course,
            $from,
            $to
        );
        $incomeStats = $incomeStatsQueryBuilder->getQuery()->getResult();

        $select = 'SUM(t.refundAmount) as refunds';
        $refundStatsQueryBuilder = $this->applyFilters(
            $this->prepareQueryBuilder($select),
            $coursesIds,
            $partner,
            $isAdmin,
            $plan,
            $authorId,
            $course,
            $from,
            $to
        );
        $refundStats = $refundStatsQueryBuilder->getQuery()->getResult();

        $paymentStats = $this->getPaymentStats($customerIds, $isAdmin, $authorId, $from, $to);

        $stats = new StatsDTO(
            (int) $incomeStats[0]['sales'],
            (int) $incomeStats[0]['income'],
            (int) ($paymentStats[0]['paid']*100),
            (int) $refundStats[0]['refunds']
        );

        return $stats;
    }

    /**
     * @param string $select
     *
     * @return QueryBuilder
     */
    private function prepareQueryBuilder(string $select): QueryBuilder
    {
        return $this->entityManager->createQueryBuilder()
            ->select($select)
            ->from(TeachableTransaction::class, 't')
            ->leftJoin('t.internalCourse', 'course')
            ->leftJoin('course.owner', 'owner');

    }

    /**
     * @param QueryBuilder   $queryBuilder
     * @param array          $coursesIds
     * @param string|null    $partner
     * @param bool           $isAdmin
     * @param string|null    $plan
     * @param int|null       $authorId
     * @param string|null    $courseName
     * @param \DateTime|null $from
     * @param \DateTime|null $to
     *
     * @return QueryBuilder
     */
    private function applyFilters(QueryBuilder $queryBuilder, array $coursesIds = [], string $partner = null, bool $isAdmin = false, string $plan = null, int $authorId = null, string $courseName = null, \DateTime $from = null, \DateTime $to = null)
    {
        if (!$isAdmin) {
            $queryBuilder->andWhere('course.id IN (:coursesIds)')->setParameter('coursesIds', $coursesIds);
        }

        if ($partner) {
            $queryBuilder->andWhere('t.affiliateName = :affiliateName')->setParameter('affiliateName', $partner);
        }

        if ($plan) {
            $queryBuilder->andWhere('owner.id = :owner_id')->setParameter('owner_id', $authorId);
        }

        if ($courseName) {
            $queryBuilder->andWhere('t.course_name = :course_name')->setParameter('course_name', $courseName);
        }

        if ($authorId) {
            $queryBuilder->andWhere('owner.id = :author_id')->setParameter('author_id', $authorId);
        }

        if ($from) {
            $queryBuilder->andWhere('t.createdAt >= :from')->setParameter('from', $from);
        }

        if ($to) {
            $queryBuilder->andWhere('t.createdAt <= :to')->setParameter('to', $to);
        }

        return $queryBuilder;
    }

    /**
     * @param array          $availableCustomerIds
     * @param bool           $isAdmin
     * @param int|null       $authorId
     * @param \DateTime|null $from
     * @param \DateTime|null $to
     *
     * @return mixed
     */
    private function getPaymentStats(array $availableCustomerIds, bool $isAdmin = false, int $authorId = null, \DateTime $from = null, \DateTime $to = null)
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('SUM(p.amount) as paid')
            ->from(CustomerPayment::class, 'p')
            ->leftJoin('p.customer', 'customer');

        if ($authorId) {
            $qb
                ->where('customer.id = :customer_id')
                ->setParameter('customer_id', $authorId);
        } else {
            $qb
                ->where('customer.id IN (:customer_ids)')
                ->setParameter('customer_ids', $availableCustomerIds);
        }

        if ($from) {
            $qb->andWhere('p.paymentDate >= :from')->setParameter('from', $from);
        }

        if ($to) {
            $qb->andWhere('p.paymentDate <= :to')->setParameter('to', $to);
        }

        return $qb->getQuery()->getResult();
    }
}