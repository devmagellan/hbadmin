<?php

namespace HB\AdminBundle\Form;

use HB\AdminBundle\Entity\SaleFunnelOrganicPriceBlockThesis;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class SaleFunnelOrganicPriceBlockThesisType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description', TextType::class, [
                'label'       => 'Добавьте список наполнения тарифного плана (макс '.SaleFunnelOrganicPriceBlockThesis::MAX_ADDITIONAL_THESIS.' пунктов)',
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
            'data_class' => SaleFunnelOrganicPriceBlockThesis::class,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'hb_adminbundle_sale_funnel_organic_price_block_additional_thesis';
    }

}