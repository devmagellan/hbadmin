<?php

namespace HB\AdminBundle\Form\SaleFunnel\OneTimeOffer;

use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelOneTimeOffer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OneTimeOfferType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $two_step_available = $options['two_step'];

        $levelChoices = ['Одноступенчатое' => SalesFunnelOneTimeOffer::ONE_STEP_LEVEL];

        if ($two_step_available) {
            $levelChoices['Двухступенчатое'] = SalesFunnelOneTimeOffer::TWO_STEP_LEVEL;
        }

        $builder
            ->add('level', ChoiceType::class, [
                'label'    => 'Тип предложения',
                'required' => true,
                'choices'  => $levelChoices,
            ])
            ->add('forWebinar', CheckboxType::class, [
                'label'    => 'Вебинар (доступен только для одноступенчатой ОТО)',
                'required' => false,
            ])
            ->add('forEducational', CheckboxType::class, [
                'label'    => 'Образовательная воронка',
                'required' => false,
            ])
            ->add('forLeadMagnet', CheckboxType::class, [
                'label'    => 'Сбор лидов',
                'required' => false,
            ])
            ->add('forMiniCourse', CheckboxType::class, [
                'label'    => 'Мини курс',
                'required' => false,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => SalesFunnelOneTimeOffer::class,
            'method' => 'POST'
        ))
            ->setRequired('two_step');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'one_time_offer_targeting';
    }

}
