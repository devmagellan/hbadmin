<?php

namespace HB\AdminBundle\Form\SaleFunnel\LeadCollection;

use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelLeadCollection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class LeadCollectionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('leadMagnetOrganic', CheckboxType::class, [
                'label'    => 'Целевая страница',
                'required' => false,
            ])
            ->add('leadMagnetWebinar', CheckboxType::class, [
                'label'    => 'Вебинар',
                'required' => false,
            ])
            ->add('leadMagnetEducation', CheckboxType::class, [
                'label'    => 'Образовательная воронка',
                'required' => false,
            ])
            ->add('leadMagnetDownSell', CheckboxType::class, [
                'label'    => 'Выгодная формула',
                'required' => false,
            ])
            ->add('leadMagnetLayerCake', CheckboxType::class, [
                'label'    => 'Слоеный пирог',
                'required' => false,
            ])
            ->add('title', TextType::class, [
                'label'       => 'Название',
                'required'    => true,
                'constraints' => [
                    new NotBlank(),
                    new Length(['max' => 70]),
                ],
            ])
            ->add('description', TextareaType::class, [
                'label'       => 'Краткое описание (призывающее взять этот бонус)',
                'required'    => true,
                'constraints' => [
                    new NotBlank(),
                    new Length(['max' => 150]),
                ],
            ])
            ->add('fullDescription', TextareaType::class, [
                'label'       => 'Развернутое описание (это детальное описание лидмагнита в письмо, для рассылки, побуждающее подписчика воспользоваться бонусом)',
                'required'    => true,
                'attr'        => ['rows' => 5],
                'constraints' => [
                    new NotBlank(),
                    new Length(['max' => 500]),
                ],
            ])
            ->add('buttonText', TextType::class, [
                'label'       => 'Текст кнопки',
                'required'    => true,
                'constraints' => [
                    new NotBlank(),
                    new Length(['max' => 30]),
                ],
            ]);

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SalesFunnelLeadCollection::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'hb_adminbundle_sale_funnel_lead_collection';
    }

}
