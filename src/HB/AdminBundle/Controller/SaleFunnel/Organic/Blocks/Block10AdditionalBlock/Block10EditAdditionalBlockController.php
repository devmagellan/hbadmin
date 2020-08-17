<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\Organic\Blocks\Block10AdditionalBlock;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\AdditionalBlock;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelOrganic;
use HB\AdminBundle\Form\SaleFunnel\AdditionalBlockEditType;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\FormHandler;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Block10EditAdditionalBlockController
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var FormHandler
     */
    private $formHandler;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var CourseAccessService
     */
    private $courseAccessService;

    /**
     * Block10EditAdditionalBlockController constructor.
     *
     * @param \Twig_Environment      $twig
     * @param FormHandler            $formHandler
     * @param FormFactoryInterface   $formFactory
     * @param EntityManagerInterface $entityManager
     * @param CourseAccessService    $courseAccessService
     */
    public function __construct(\Twig_Environment $twig, FormHandler $formHandler, FormFactoryInterface $formFactory, EntityManagerInterface $entityManager, CourseAccessService $courseAccessService)
    {
        $this->twig = $twig;
        $this->formHandler = $formHandler;
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
        $this->courseAccessService = $courseAccessService;
    }


    /**
     * @param Request         $request
     * @param AdditionalBlock $additionalBlock
     *
     * @return Response
     */
    public function handleAction(AdditionalBlock $additionalBlock, Request $request)
    {
        $funnel = $this->getAdditionalBlockFunnel($additionalBlock);
        if ($funnel) {
            $this->courseAccessService->resolveUpdateAccess($funnel);
        }

        $form = $this->formFactory->create(AdditionalBlockEditType::class, $additionalBlock);

        if ($this->formHandler->handle($additionalBlock, $request, $form)) {
            $this->entityManager->persist($additionalBlock);
            $this->entityManager->flush();

            return new JsonResponse(['status' => 'success']);
        }

        $content = $this->twig->render("@HBAdmin/SaleFunnel/Organic/blocks/block10-edit.html.twig", [
            'form'  => $form->createView(),
            'block' => $additionalBlock,
        ]);

        return new Response($content);
    }

    /**
     * @param AdditionalBlock $additionalBlock
     *
     * @return SalesFunnelOrganic
     */
    private function getAdditionalBlockFunnel(AdditionalBlock $additionalBlock): SalesFunnelOrganic
    {
        return $this->entityManager->createQueryBuilder()
            ->select('organic_funnel')
            ->from(SalesFunnelOrganic::class, 'organic_funnel')
            ->leftJoin('organic_funnel.block10AdditionalBlocks', 'add_block')
            ->where('add_block.id = :add_block_id')
            ->setParameter('add_block_id', $additionalBlock->getId())
            ->getQuery()->getOneOrNullResult();
    }
}