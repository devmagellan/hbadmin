<?php

namespace HB\AdminBundle\Form\SaleFunnel\BrokenBasket;

use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelBrokenBasket;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BrokenBasketType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choices = SalesFunnelBrokenBasket::getPossibleDiscounts();

        $builder
            ->add('discountPercent', ChoiceType::class, [
                'label' => 'Скидка',
                'required' => true,
                'choices' => $choices
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => SalesFunnelBrokenBasket::class
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'broken_basket';
    }
}
