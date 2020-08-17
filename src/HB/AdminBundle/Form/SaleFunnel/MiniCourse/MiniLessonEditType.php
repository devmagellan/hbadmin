<?php

namespace HB\AdminBundle\Form\SaleFunnel\MiniCourse;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use HB\AdminBundle\Entity\SaleFunnel\MiniCourse\MiniLesson;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class MiniLessonEditType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label'       => 'Название урока',
                'required'    => true,
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('lessonText', CKEditorType::class, [
                'label'    => 'Текст урока (не обязательно)',
                'config'   => ['toolbar' => 'basic'],
                'required' => false,
            ])
            ->add('homeworkText', CKEditorType::class, [
                'label'    => 'Текст домашнего задания (не обязательно)',
                'config'   => ['toolbar' => 'basic'],
                'required' => false,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => MiniLesson::class,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'hb_adminbundle_sale_funnel_mini_lesson';
    }
}
