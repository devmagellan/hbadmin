<?php

namespace HB\AdminBundle\Repository;

use Doctrine\ORM\QueryBuilder;
use HB\AdminBundle\Entity\Customer;

class TeachableTransactionRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param array $ids
     * @param bool  $isAdmin
     *
     * @return QueryBuilder
     */
    public function getTransactionsByIdsQueryBuilder(array $ids, bool $isAdmin = false)
    {
        $qb = $this->createQueryBuilder('t')
            ->select('t')
            ->orderBy('t.createdAt', 'DESC');

        if (!$isAdmin) {
            $qb->where('t.internalCourse IN (:ids)')->setParameter('ids', $ids);
        }

        $qb->orderBy('t.createdAt', 'DESC');

        return $qb;
    }

    /**
     * Get courses distinct for filters
     *
     * @param array $ids
     * @param bool  $isAdmin
     *
     * @return array
     */
    public function getDistinctCourses(array $ids, bool $isAdmin = false)
    {
        return $this->getDistinctChoices($ids, 'course_name', 'courseName', $isAdmin);
    }

    /**
     * Get courses distinct for filters
     *
     * @param array $ids
     * @param bool  $isAdmin
     *
     * @return array
     */
    public function getDistinctAffiliates(array $ids, bool $isAdmin = false)
    {
        return $this->getDistinctChoices($ids, 'affiliateName', 'affiliateName', $isAdmin);
    }

    /**
     * @param array $ids
     * @param bool  $isAdmin
     *
     * @return array
     */
    public function getDistinctPlans(array $ids, bool $isAdmin = false)
    {
        return $this->getDistinctChoices($ids, 'product_plan', 'productPlan', $isAdmin);
    }

    /**
     * @return array | Customer[]
     */
    public function getDistinctAuthorsIds()
    {
        return $this->createQueryBuilder('t')
            ->select('DISTINCT(customer.id) as id')
            ->leftJoin('t.internalCourse', 'course')
            ->leftJoin('course.owner', 'customer')
            ->getQuery()->getResult();
    }

    /**
     * Universal function for filter choices
     *
     * @param array        $ids
     * @param string       $param
     * @param string       $alias
     * @param bool         $isAdmin
     *
     * @return array
     */
    private function getDistinctChoices(array $ids, string $param, string $alias, bool $isAdmin = false)
    {

        $qb = $this->createQueryBuilder('t')
            ->select('DISTINCT(t.'.$param.') as '.$alias)
            ->orderBy('t.'.$param, 'ASC');

        if (!$isAdmin) {
            $qb->where('t.internalCourse IN (:ids)')->setParameter('ids', $ids);
        }

        $result = $qb->getQuery()->getResult();

        $choices = [];

        foreach ($result as $item) {
            if ($item[$alias]) {
                $choices[$item[$alias]] = $item[$alias];
            }
        }

        return $choices;

    }

}
