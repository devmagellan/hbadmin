<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\Customer;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Service\CustomerAccessService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;

class ListController
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
     * @var CustomerAccessService
     */
    private $customerAccessService;

    /**
     * ListController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param \Twig_Environment      $twig
     * @param PaginatorInterface     $paginator
     * @param CustomerAccessService  $customerAccessService
     */
    public function __construct(EntityManagerInterface $entityManager, \Twig_Environment $twig, PaginatorInterface $paginator, CustomerAccessService $customerAccessService)
    {
        $this->entityManager = $entityManager;
        $this->twig = $twig;
        $this->paginator = $paginator;
        $this->customerAccessService = $customerAccessService;
    }


    /**
     * @param int $page
     *
     * @return Response
     */
    public function handleAction(int $page = 1): Response
    {
        $content = $this->twig->render("@HBAdmin/Customer/list.html.twig", [
            'customers' => $this->paginator->paginate(
                $this->customerAccessService->getCustomersListQuery(),
                $page
            ),
        ]);

        return new Response($content);
    }
}
