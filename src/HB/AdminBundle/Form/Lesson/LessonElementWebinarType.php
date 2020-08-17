<?php

namespace HB\AdminBundle\Form\Lesson;

use HB\AdminBundle\Entity\LessonElement;
use HB\AdminBundle\Helper\TimeZoneList;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TimezoneType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;

class LessonElementWebinarType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('webinarAt', DateTimeType::class, [
                'label'                     => 'Дата и время вебинара',
                'format'                    => '',
                'choice_translation_domain' => true,
                'minutes'                   => [0, 15, 30, 45],
                'constraints'               => [
                    new GreaterThan('+1 hour'),
                ],
            ])
            ->add('webinarTimezone', TimezoneType::class, [
                'label'             => 'Часовой пояс',
                'choices'           => TimeZoneList::getTimezones(\DateTimeZone::EUROPE | \DateTimeZone::ASIA),
                'required'          => true,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => LessonElement::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'hb_adminbundle_lesson_element_webinar';
    }
}
