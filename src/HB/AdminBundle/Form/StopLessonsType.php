<?php

namespace HB\AdminBundle\Form;

use HB\AdminBundle\Entity\Embedded\StopLessons;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StopLessonsType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('previousLessonsMark', CheckboxType::class, [
                'label'    => 'просмотрел все предыдущие уроки',
                'required' => false,
            ])
            ->add('viewedVideosMark', CheckboxType::class, [
                'label'    => 'просмотрел не менее 90% всех пройденных видео',
                'required' => false,
            ])
            ->add('testPassedMark', CheckboxType::class, [
                'label'    => 'ответил на все тесты',
                'required' => false,
            ])
            ->add('testsMinPercentPassed', NumberType::class, [
                'label'    => '% правильных ответов  для доступа к следующему уроку. Если вы оставите это поле пустым, то для доступа к следующим урокам необходимо будет просто закончить тест',
                'required' => false,
            ])
            ->add('testsMaxAttempts', NumberType::class, [
                'label'    => 'максимальное количество попыток',
                'required' => false,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => StopLessons::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'stop_lessons';
    }


}
