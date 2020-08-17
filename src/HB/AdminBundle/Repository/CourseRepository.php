<?php

namespace HB\AdminBundle\Repository;


use Doctrine\ORM\Query;
use HB\AdminBundle\Entity\Customer;
use HB\AdminBundle\Entity\Types\CourseStatusType;

class CourseRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param Customer $customer
     * @param array    $filters
     *
     * @return Query
     */
    public function getCoursesQuery(Customer $customer, array $filters = []): Query
    {
        if ($customer->isAdmin()) {
            $query = $this->createQueryBuilder('course')
                ->select('course')
                ->orderBy('course.dateCreatedAt', 'DESC');
        } else if ($customer->isProducer()) {
            $query = $this->createQueryBuilder('course')
                ->select('course')
                ->leftJoin('course.owner', 'owner')
                ->leftJoin('owner.producer', 'producer')
                ->where('owner.id = :producer_id')
                ->orWhere('producer.id = :producer_id')
                ->setParameter('producer_id', $customer->getId())
                ->orderBy('course.dateCreatedAt', 'DESC');
        } else if ($customer->isAuthor()) {
            $query = $this->createQueryBuilder('course')
                ->select('course')
                ->leftJoin('course.owner', 'owner')
                ->where('owner.id = :author_id')
                ->setParameter('author_id', $customer->getId())
                ->orderBy('course.dateCreatedAt', 'DESC');
        } else if ($customer->isManager()) {
            $author = $customer->getOwner();

            $query = $this->createQueryBuilder('course')
                ->select('course')
                ->leftJoin('course.owner', 'owner')
                ->where('owner.id = :author_id')
                ->setParameter('author_id', $author->getId())
                ->orderBy('course.dateCreatedAt', 'DESC');
        } else {
            throw new \InvalidArgumentException(sprintf('Список курсов не найден для пользователя id: %s', $customer->getId()));
        }

        if (!empty($filters)) {
            if (isset($filters['status']) && strlen($filters['status']) > 0) {
                $status = $filters['status'];

                if (CourseStatusType::SANDBOX === $status) {
                    $query->andWhere('course.sandbox = :sandbox')->setParameter('sandbox', true);
                } else {
                    $query->andWhere('course.status = :status')->setParameter('status', $status);
                    $query->andWhere('course.sandbox = :sandbox')->setParameter('sandbox', false);
                }
            }
            if (isset($filters['course_id']) && strlen($filters['course_id']) > 0) {
                $query->andWhere('course.id = :courseId')->setParameter('courseId', $filters['course_id']);
            }

            if (isset($filters['external_id']) && strlen($filters['external_id']) > 0) {
                $query->andWhere('course.teachableId = :external_id')->setParameter('external_id', $filters['external_id']);
            }

            if (isset($filters['course_name']) && strlen($filters['course_name']) > 0) {
                $query->andWhere('course.info.title LIKE :course_name')->setParameter('course_name', "%".$filters['course_name']."%");
            }

            if (isset($filters['author']) && strlen($filters['author']) > 0) {
                $query->leftJoin('course.owner', 'owner')
                    ->andWhere('owner.firstName LIKE :author OR owner.email LIKE :author OR owner.surname LIKE :author ')->setParameter('author', "%".$filters['author']."%");
            }
        }

        return $query->getQuery();
    }
}
