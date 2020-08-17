<?php

namespace HB\AdminBundle\Form\Quiz;

use HB\AdminBundle\Entity\QuizQuestionAnswer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuizAnswerType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('isRight', null, [
                'label' => 'верный'
            ])
            ->add('answerText', TextType::class, [
                'label' => false,
                'attr' => ['placeholder' => 'Введите текст ответа']
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => QuizQuestionAnswer::class
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'hb_adminbundle_quiz_answer';
    }


}
