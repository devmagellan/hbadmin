<?php

namespace HB\AdminBundle\Form;

use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Entity\Customer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CourseType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $authors = $options['authors'];
        $builder
            ->add('owner', EntityType::class, [
                'class' => Customer::class,
                'choices' => $authors,
                'label' => 'Автор',
            ])
            ->add('info', CourseInfoType::class, [
                'label' => false,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Course::class,
        ))
        ->setRequired('authors');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'hb_adminbundle_course_create';
    }

}