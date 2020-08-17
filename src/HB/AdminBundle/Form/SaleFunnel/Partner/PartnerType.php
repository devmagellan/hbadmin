<?php

namespace HB\AdminBundle\Form\SaleFunnel\Partner;

use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelPartner;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

class PartnerType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('partner', TextType::class, [
                'label'    => 'Укажите Имя и Фамилию Вашего партнера',
                'required' => true,
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('email', EmailType::class, [
                'label'    => 'Укажите Емейл Вашего партнера',
                'required' => true,
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('awardPercent', NumberType::class, [
                'label'    => 'Укажите Размер вознаграждения партнера (% от продаж)',
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new Range(['min' => 0.1, 'max' => 99])
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
            'data_class' => SalesFunnelPartner::class,
            'method' => 'POST'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'hb_adminbundle_sale_funnel_partner';
    }
}
