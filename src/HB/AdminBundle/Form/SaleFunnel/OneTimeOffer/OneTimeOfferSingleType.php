<?php

namespace HB\AdminBundle\Form\SaleFunnel\OneTimeOffer;

use HB\AdminBundle\Entity\SaleFunnel\OneTimeOffer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;

class OneTimeOfferSingleType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description', TextType::class, [
                'label'       => 'Введите название продукта, который вы будете продавать на странице единоразового предложения',
                'required'    => true,
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('fullPrice', NumberType::class, [
                'label'       => 'Обычная цена (будет перечеркнута)',
                'required'    => true,
                'constraints' => [
                    new NotBlank(),
                    new GreaterThan(['value' => 0]),
                ],
            ])
            ->add('discountPrice', NumberType::class, [
                'label'       => 'Специальная цена (по ней можно будет купить)',
                'required'    => true,
                'constraints' => [
                    new NotBlank(),
                    new GreaterThan(['value' => 0]),
                ],
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => OneTimeOffer::class,
            'method'     => 'POST',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'one_time_offer_single';
    }

}
