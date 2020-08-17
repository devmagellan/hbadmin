<?php

namespace HB\AdminBundle\Form\SaleFunnel\Organic;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelOrganic;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class Block5RecommendedType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('block5Type', ChoiceType::class, [
                'label'    => 'Тип',
                'choices'  => [
                    'Курс рекомедуется'       => SalesFunnelOrganic::BLOCK5_TYPE_RECOMMEND,
                    'Требования к участникам' => SalesFunnelOrganic::BLOCK5_TYPE_REQUIRED,
                ],
                'required' => true,
            ])
            ->add('block5Text', CKEditorType::class, [
                'label'       => 'Описание (Добавьте требования к участникам курса или опишите вашу целевую аудиторию)',
                'required'    => true,
                'config'      => ['toolbar' => 'organic_toolbar'],
                'constraints' => [
                    new NotBlank(),
                ],
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
        return 'hb_adminbundle_sale_funnel_organic_block5_recommended';
    }
}
