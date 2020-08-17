<?php

namespace HB\AdminBundle\Form\SaleFunnel\Organic;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelOrganic;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class Block6AuthorType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('block6AuthorInfo', TextType::class, [
                'label'       => 'Кто вы? (Например: психолог, бизнес-тренер, фотограф)',
                'required'    => true,
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('block6AuthorExperience', CKEditorType::class, [
                'label'       => 'Почему вы имеет право вести курс ? (Регалии, Опыт и т.д.)',
                'required'    => true,
                'config'      => ['toolbar' => 'organic_toolbar'],
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('block6AuthorVideoFromBanner', null, [
                'label' => 'Использовать видео с первого экрана',
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
        return 'hb_adminbundle_sale_funnel_organic_block6_author';
    }
}
