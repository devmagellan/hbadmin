<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\Course;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Entity\Customer;
use HB\AdminBundle\Service\SandBoxCourseService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ListController extends Controller
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
     * @var SandBoxCourseService
     */
    private $sandBoxCourseService;

    /**
     * ListController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param \Twig_Environment      $twig
     * @param PaginatorInterface     $paginator
     * @param TokenStorageInterface  $tokenStorage
     * @param SandBoxCourseService   $sandBoxCourseService
     */
    public function __construct(EntityManagerInterface $entityManager, \Twig_Environment $twig, PaginatorInterface $paginator, TokenStorageInterface $tokenStorage, SandBoxCourseService $sandBoxCourseService)
    {
        $this->entityManager = $entityManager;
        $this->twig = $twig;
        $this->paginator = $paginator;
        $this->currentUser = $tokenStorage->getToken()->getUser();
        $this->sandBoxCourseService = $sandBoxCourseService;
    }

    /**
     * @param int     $page
     * @param Request $request
     *
     * @return Response
     */
    public function handleAction(Request $request, int $page = 1): Response
    {
        $filters = $request->get('f', []);
        $query = $this->entityManager->getRepository(Course::class)->getCoursesQuery($this->currentUser, $filters);

        $courses = $this->paginator->paginate($query, $page);

        $content = $this->twig->render("@HBAdmin/Course/list.html.twig", [
            'courses'     => $courses,
            'has_sandbox' => $this->sandBoxCourseService->hasSandBox($this->currentUser),
            'filters'     => $filters,
            'use_filters' => empty($filters) ? false : true,
        ]);

        return new Response($content);
    }
}
