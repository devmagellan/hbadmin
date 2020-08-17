<?php

namespace HB\AdminBundle\Form\SaleFunnel\PostSale;

use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelPostSale;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostSaleType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $discounts = SalesFunnelPostSale::getPossibleDiscounts();
        $choices = ['' => ''];
        foreach ($discounts as $discount) {
            $choices[$discount.' %'] = $discount;
        }

        $builder
            ->add('activateAfterDays', CheckboxType::class, [
                'label'    => 'Активировать',
                'required' => false,
            ])
            ->add('discountPercent', ChoiceType::class, [
                'label'    => 'Скидка',
                'choices'  => $choices,
                'required' => true,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => SalesFunnelPostSale::class,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'hb_adminbundle_sale_funnel_post_sale';
    }
}
