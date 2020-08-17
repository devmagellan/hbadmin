<?php

namespace HB\AdminBundle\Form;

use HB\AdminBundle\Entity\LessonSection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class LessonSectionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label'       => 'Название раздела',
                'constraints' => [
                    new Length(['max' => 200]),
                ],
            ])
            ->add('type', ChoiceType::class, [
                'label'    => 'Тип раздела',
                'choices'  => [
                    '1. обычный'                => LessonSection::TYPE_DEFAULT,
                    '2. откроется через N дней' => LessonSection::TYPE_AFTER_DAYS,
                    '3. откроется по дате'      => LessonSection::TYPE_BY_DATE,
                ],
                'required' => true,
            ])
            ->add('afterDays', NumberType::class, [
                'label'    => 'для типа 2. кол-во дней ',
                'required' => false,
            ])
            ->add('byDate', DateType::class, [
                'label'    => 'для типа 3. дата открытия раздела ',
                'widget'   => 'single_text',
                'required' => false,
                'html5'    => false,
                'attr'     => ['class' => 'datepicker'],
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => LessonSection::class,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'hb_adminbundle_lessonsection';
    }


}
