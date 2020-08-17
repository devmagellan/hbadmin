<?php

namespace HB\AdminBundle\Form\SaleFunnel\OneTimeOffer;

use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelOneTimeOffer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class DescriptionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('thankText', TextareaType::class, [
                'label'       => 'Текст благодарности',
                'required'    => true,
                'attr'        => ['rows' => 5],
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('description', TextareaType::class, [
                'label'       => 'Введение',
                'required'    => true,
                'attr'        => ['rows' => 5],
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('videoText', TextareaType::class, [
                'label' => 'Видеопризыв к действию - необязательное',
                'attr'  => ['rows' => 5],
            ])
            ->add('additionalText', TextareaType::class, [
                'label' => 'Дополнительный текстовый блок',
                'attr'  => ['rows' => 5],
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SalesFunnelOneTimeOffer::class,
            'method'     => 'POST',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'one_time_offer_description';
    }

}
