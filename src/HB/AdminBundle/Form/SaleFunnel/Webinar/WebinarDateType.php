<?php

namespace HB\AdminBundle\Form\SaleFunnel\Webinar;

use HB\AdminBundle\Entity\SaleFunnel\Webinar\WebinarDate;
use HB\AdminBundle\Helper\TimeZoneList;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TimezoneType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;

class WebinarDateType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('time', DateTimeType::class, [
                'widget'   => 'single_text',
                'label'    => 'Время проведения вебинара',
                'format'   => 'yyyy-MM-dd HH:mm',
                'required' => true,
                'html5'    => false,
                'attr'     => ['class' => 'datetimepicker-webinar'],
                'constraints' => [
                    new GreaterThan(new \DateTime('+1 hours'))
                ],
            ])
            ->add('timezone', TimezoneType::class, [
                'label'    => 'Часовой пояс',
                'choices'  => TimeZoneList::getTimezones(\DateTimeZone::EUROPE | \DateTimeZone::ASIA),
                'required' => true,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => WebinarDate::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'webinar_date';
    }
}
