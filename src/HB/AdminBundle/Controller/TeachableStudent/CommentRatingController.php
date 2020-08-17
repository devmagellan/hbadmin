<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\TeachableStudent;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\Teachable\TeachableComment;
use HB\AdminBundle\Service\StudentProgressAggregator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CommentRatingController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var StudentProgressAggregator
     */
    private $studentProgressAggregator;

    /**
     * CommentRatingController constructor.
     *
     * @param EntityManagerInterface    $entityManager
     * @param StudentProgressAggregator $studentProgressAggregator
     */
    public function __construct(EntityManagerInterface $entityManager, StudentProgressAggregator $studentProgressAggregator)
    {
        $this->entityManager = $entityManager;
        $this->studentProgressAggregator = $studentProgressAggregator;
    }

    /**
     * @param TeachableComment $comment
     * @param Request          $request
     *
     * @return JsonResponse
     */
    public function handleAction(TeachableComment $comment, Request $request)
    {
        $rating = $request->get('rating', null);

        if ($comment) {
            $comment->setRating((int) $rating);
            $this->entityManager->persist($comment);
            $this->entityManager->flush();

            $this->studentProgressAggregator->update($comment->getStudentEmail(), $comment->getCourseId());
        }

        return new JsonResponse(['status' => 'success']);
    }
}