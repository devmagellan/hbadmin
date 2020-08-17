<?php

namespace HB\AdminBundle\Form\Lesson;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use HB\AdminBundle\Entity\LessonElement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LessonElementTextType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('text', CKEditorType::class, [
                'label'  => false,
                'attr'   => ['rows' => 10],
                'config' => ['toolbar' => 'lessons_toolbar'],
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
        return 'hb_adminbundle_lesson_element_text';
    }


}
