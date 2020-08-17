<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Service;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Entity\CoursePriceBlock;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelOrganic;
use HB\AdminBundle\Entity\SaleFunnelOrganicPriceBlockSetting;
use HB\AdminBundle\Entity\LessonSection;

class PriceBlockSettingService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * PriceBlockSettingService constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getPriceBlockSetting(SalesFunnelOrganic $funnel, string $coursePriceBlockType): SaleFunnelOrganicPriceBlockSetting
    {
        $priceBlockSetting = $this->entityManager->createQueryBuilder()
            ->select('setting')
            ->from(SaleFunnelOrganicPriceBlockSetting::class, 'setting')
            ->leftJoin('setting.coursePriceBlock', 'course_price_block')
            ->where('setting.funnel = :funnel')
            ->andWhere('course_price_block.type = :type')
            ->setParameters([
                'funnel' => $funnel,
                'type'   => $coursePriceBlockType,
            ])
            ->getQuery()
            ->getOneOrNullResult();

        if (!$priceBlockSetting) {
            $priceBlock = $this->entityManager->createQueryBuilder()
                ->select('price_block')
                ->from(CoursePriceBlock::class, 'price_block')
                ->where('price_block.type = :type')
                ->setParameter('type', $coursePriceBlockType)
                ->getQuery()->getOneOrNullResult();
            if (!$priceBlock) {
                throw new \InvalidArgumentException('Базовый тарифный план не найден');
            }

            $priceBlockSetting = new SaleFunnelOrganicPriceBlockSetting($funnel, $priceBlock);
        }

        return $priceBlockSetting;
    }

    /**
     * @param Course $course
     * @param string $coursePriceBlockType
     *
     * @return int
     */
    public function getLessonCount(Course $course, string $coursePriceBlockType)
    {
        return (int) $this->entityManager->createQueryBuilder()->
        select('COUNT(lesson)')
            ->from(LessonSection::class, 'lesson_section')
            ->leftJoin('lesson_section.lessons', 'lesson')
            ->leftJoin('lesson_section.priceBlocks', 'price_block')
            ->where('lesson_section.course = :course')
            ->andWhere('price_block.type = :type')
            ->setParameters([
                'course' => $course,
                'type'   => $coursePriceBlockType,
            ])
            ->getQuery()->getSingleScalarResult();
    }

    /**
     * Get price block with checking customer funnel price block relation
     */
    public function getPriceBlock(SalesFunnelOrganic $funnel, string $coursePriceBlockType): ?SaleFunnelOrganicPriceBlockSetting
    {
        $priceBlockSetting = null;

        /** @var CoursePriceBlock $block */
        foreach ($funnel->getPriceBlocks() as $block) {
            if ($coursePriceBlockType === $block->getType()) {
                $priceBlockSetting = $this->entityManager->createQueryBuilder()
                    ->select('setting', 'theses')
                    ->from(SaleFunnelOrganicPriceBlockSetting::class, 'setting')
                    ->leftJoin('setting.coursePriceBlock', 'course_price_block')
                    ->leftJoin('setting.theses', 'theses')
                    ->where('setting.funnel = :funnel')
                    ->andWhere('course_price_block.type = :type')
                    ->setParameters([
                        'funnel' => $funnel,
                        'type'   => $coursePriceBlockType,
                    ])
                    ->getQuery()
                    ->getOneOrNullResult();
                break;
            }
        }

        return $priceBlockSetting;
    }
}