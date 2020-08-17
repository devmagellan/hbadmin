<?php

namespace HB\AdminBundle\Form\SaleFunnel\Webinar;

use HB\AdminBundle\Entity\CoursePriceBlock;
use HB\AdminBundle\Entity\SaleFunnel\Webinar\WebinarOffer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;

class Block6DownSellOfferBlockType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $price_blocks = $options['price_blocks'];
        $blocks = [];

        /** @var CoursePriceBlock $block */
        foreach ($price_blocks as $block) {
            $blocks[$block->getTitle()] = $block->getTitle();
        }

        $builder
            ->add('title', ChoiceType::class, [
                'label' => 'Тарифный план',
                'choices' => $blocks,
                'required' => true
            ])
            ->add('price', NumberType::class, [
                'label' => 'Цена со скидкой',
                'constraints' => [
                    new NotBlank(),
                    new GreaterThan(['value' => 0]),
                ],
            ])
            ->add('months', NumberType::class, [
                'label' => 'Рассрочка (месяцев) - не обязательно',
                'required' => false,
                'constraints' => [
                    new GreaterThan(['value' => 0])
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
            'data_class' => WebinarOffer::class,
        ))
        ->setRequired('price_blocks');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'hb_adminbundle_sale_funnel_webinar_b6_downsell_offer_block';
    }

}
