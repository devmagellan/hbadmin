<?php

namespace HB\AdminBundle\Form\SaleFunnel\Organic;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelOrganic;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class Block2MainInfoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('block2CourseInfo', CKEditorType::class, [
                'label'       => 'Описание курса',
                'required'    => true,
                'config'      => ['toolbar' => 'organic_toolbar'],
                'constraints' => [
                    new NotBlank(),
                    new Length(['max' => 700])
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
        return 'hb_adminbundle_sale_funnel_organic_block2_main_info';
    }
}
