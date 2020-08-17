<?php

namespace HB\AdminBundle\Form\Quiz;

use HB\AdminBundle\Entity\QuizQuestion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuizQuestionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('questionText', TextType::class, [
                'label' => false,
                'attr' => ['placeholder' => 'Введите текст вопроса']
            ])
            ->add('answers', CollectionType::class, [
                'label' => false,
                'entry_type' => QuizAnswerType::class,
                'prototype' => true,
                'allow_add' => true
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => QuizQuestion::class
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'hb_adminbundle_quiz_question';
    }


}
