<?php

namespace HB\AdminBundle\Form\SaleFunnel\HotLeads;

use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelHotLeads;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Range;

class HotLeadsType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('installmentPlan', CheckboxType::class, [
                'label' => 'Предложить рассрочку на тарифный план базовый на Х месяцев',
                'required' => false
            ])
            ->add('installmentPlanMonths', NumberType::class, [
                'label' => 'Укажите количество месяцев рассрочки',
                'required' => false,
                'constraints' => [
                    new Range(['min' => 0]),
                    new Length(['min' => 0, 'max' => 2])
                ]
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => SalesFunnelHotLeads::class
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'hb_adminbundle_sale_funnel_hot_leads';
    }
}
