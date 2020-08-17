<?php

namespace HB\AdminBundle\Form\SaleFunnel\OneTimeOffer;

use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelOneTimeOffer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LinkType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('link', TextareaType::class, [
                'label'       => 'Ссылка на google документ (доступ к файлу должен быть открыт по ссылке с возможностью комментирования/редактирования)',
                'required'    => false,
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
        return 'one_time_offer_link';
    }

}
