<?php

namespace HB\AdminBundle\Form\SaleFunnel\CrossSale;

use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelCrossSale;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CrossSaleType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('participateAuthorClub', CheckboxType::class, [
                'label'   => 'Выбрать курсы из Клуба Авторов со скидкой 10% / где остальная комиссия для этого автора 40%',
                'required' => false
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => SalesFunnelCrossSale::class,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'hb_adminbundle_sale_funnel_cross_sale';
    }
}
