<?php

namespace HB\AdminBundle\Form\SaleFunnel\Organic;

use Doctrine\ORM\EntityRepository;
use HB\AdminBundle\Entity\CoursePriceBlock;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelOrganic;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PriceBlocksType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('priceBlocks', EntityType::class, [
                'class'         => CoursePriceBlock::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('cb')
                        ->select('cb')
                        ->orderBy('cb.priority', 'ASC');
                },
                'label'         => 'Тарифные планы',
                'expanded'      => true,
                'multiple'      => true,
                'label_attr'          => ['for' => ''],
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => SalesFunnelOrganic::class,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'hb_adminbundle_sale_funnel_organic_price_blocks';
    }
}
