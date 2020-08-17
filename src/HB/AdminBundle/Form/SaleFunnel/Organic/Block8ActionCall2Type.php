<?php

namespace HB\AdminBundle\Form\SaleFunnel\Organic;

use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelOrganic;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Block8ActionCall2Type extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('block8Title', TextType::class, [
                'label'      => 'Заголовок',
                'empty_data' => SalesFunnelOrganic::BLOCK8_TITLE,
                'required'   => false,
            ])
            ->add('block8Subtitle', TextType::class, [
                'label'      => 'Подзаголовок',
                'empty_data' => SalesFunnelOrganic::BLOCK8_SUBTITLE,
                'required'   => false,
            ])
            ->add('block8ButtonText', TextType::class, [
                'label'      => 'Текст кнопки',
                'empty_data' => SalesFunnelOrganic::BLOCK8_BUTTON_TEXT,
                'required'   => false,
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
        return 'hb_adminbundle_sale_funnel_organic_block8_action_call_2';
    }
}
