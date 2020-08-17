<?php

namespace HB\AdminBundle\Form\SaleFunnel\Webinar;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelWebinar;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class TargetPageType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label'       => 'Название Мастер Класса',
                'required'    => true,
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 20, 'max' => 70,]),
                ],
            ])
            ->add('description', CKEditorType::class, [
                'label'       => 'Описание вебинара, для кого он проводится, целевая аудитория',
                'config'      => ['toolbar' => 'basic'],
                'required'    => true,
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 100, 'max' => 500,]),
                ],
            ])
            ->add('dates', CollectionType::class, [
                'entry_type' => WebinarDateType::class,
                'label'      => false,
                'prototype'  => true,
                'allow_add'  => true,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SalesFunnelWebinar::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'hb_adminbundle_sale_funnel_webinar';
    }


}
