<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\Finance;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\Teachable\TeachableTransaction;
use Symfony\Component\HttpFoundation\Response;

class WithoutRelationTransactionCountController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * WithoutRelationTransactionCountController constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return mixed
     */
    public function handleAction()
    {
        $count =  $this->entityManager->createQueryBuilder()
            ->select('COUNT(t.id)')
            ->from(TeachableTransaction::class, 't')
            ->where('t.internalCourse IS NULL')
            ->getQuery()
            ->getSingleScalarResult();

        return new Response($count);
    }
}