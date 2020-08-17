<?php

namespace HB\AdminBundle\Form\PriceBlockSetting;

use HB\AdminBundle\Entity\SaleFunnelOrganicPriceBlockSetting;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BasicSettingType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Свое название тарифного плана (не обязательно)',
                'required' => false
            ])
            ->add('subscriptionType', ChoiceType::class,[
                'label' => 'Тип оплаты',
                'choices' =>[
                    'Ежемесячная подписка' => SaleFunnelOrganicPriceBlockSetting::TYPE_SUBSCRIPTION_MONTHLY,
                    'Единоразовый платеж' => SaleFunnelOrganicPriceBlockSetting::TYPE_SUBSCRIPTION_ONCE
                ]
            ])
            ->add('price', NumberType::class, [
                'label' => 'Стоимость, $',
                'attr' => ['min' => 0]
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => SaleFunnelOrganicPriceBlockSetting::class
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'hb_adminbundle_course_price_block_setting_basic';
    }


}
