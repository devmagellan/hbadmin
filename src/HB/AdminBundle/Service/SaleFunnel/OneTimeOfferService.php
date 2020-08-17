<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Service\SaleFunnel;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Entity\Customer;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelOneTimeOffer;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelWebinar;

class OneTimeOfferService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * OneTimeOfferService constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param Course $course
     *
     * @return array
     */
    public function resolvePurposeUse(Course $course): array
    {
        $purposeFields = ['forWebinar', 'forLeadMagnet', 'forEducational', 'forMiniCourse'];

        $exist = [];

        foreach ($purposeFields as $field) {
            $exist[$field] = false; //$this->isPurposeInUse($course, $field);
        }

        return $exist;
    }

    /**
     * @param Course $course
     * @param string $field
     *
     * @return bool
     */
    private function isPurposeInUse(Course $course, string $field)
    {
        return (bool) $this->entityManager->createQueryBuilder()
            ->select('1')
            ->from(SalesFunnelOneTimeOffer::class, 'funnel')
            ->leftJoin('funnel.course', 'course')
            ->where('course.id = :course_id')
            ->andWhere('funnel.'.$field.' = true')
            ->setParameters([
                'course_id' => $course->getId(),
            ])
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Customer $user
     *
     * @return mixed
     */
    public function getAvailableAuthorWebinars(Customer $user)
    {
        $webinars = $this->entityManager->createQueryBuilder()
            ->select('funnel')
            ->from(SalesFunnelWebinar::class, 'funnel')
            ->innerJoin('funnel.course', 'course')
            ->innerJoin('course.owner', 'owner')
            ->where('owner.id = :owner_id')
            ->setParameter('owner_id', $user->getId())
            ->getQuery()
            ->getResult();

        return $webinars;
    }
}