<?php

namespace HB\AdminBundle\Form\PriceBlockSetting;

use Doctrine\ORM\EntityRepository;
use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Entity\SaleFunnelOrganicPriceBlockSetting;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SettingWithCoursesOptimalType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Course $current_course */
        $current_course = $options['current_course'];

        $builder
            ->add('title', TextType::class, [
                'label'    => 'Свое название тарифного плана (не обязательно)',
                'required' => false,
            ])
            ->add('subscriptionType', ChoiceType::class, [
                'label'   => 'Тип оплаты',
                'choices' => [
                    'Ежемесячная подписка' => SaleFunnelOrganicPriceBlockSetting::TYPE_SUBSCRIPTION_MONTHLY,
                    'Единоразовый платеж'       => SaleFunnelOrganicPriceBlockSetting::TYPE_SUBSCRIPTION_ONCE,
                ],
            ])
            ->add('price', NumberType::class, [
                'label' => 'Стоимость, $',
                'attr'  => ['min' => 0],
            ])
            ->add('courses', EntityType::class, [
                'class'         => Course::class,
                'label'         => 'Бонусные курсы',
                'query_builder' => function (EntityRepository $entityRepository) use ($current_course) {
                    return $entityRepository->createQueryBuilder('c')
                        ->leftJoin('c.owner', 'owner')
                        ->where('owner.id = :id')
                        ->andWhere('c.id != :current_course_id')
                        ->setParameters([
                            'id'                => $current_course->getOwner()->getId(),
                            'current_course_id' => $current_course->getId(),
                        ]);
                },
                'expanded'      => true,
                'multiple'      => true,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => SaleFunnelOrganicPriceBlockSetting::class,
        ));
        $resolver->setRequired(['current_course']);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'hb_adminbundle_course_price_block_setting_with_courses_vip_optimal';
    }


}
