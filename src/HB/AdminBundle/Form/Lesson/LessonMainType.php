<?php

namespace HB\AdminBundle\Form\Lesson;

use Doctrine\ORM\EntityRepository;
use HB\AdminBundle\Entity\Lesson;
use HB\AdminBundle\Entity\LessonSection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class LessonMainType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $courseId = $options['courseId'];

        $builder
            ->add('title', TextType::class, [
                'label'       => 'Название урока',
                'constraints' => [
                    new Length(['max' => 200]),
                ],
            ])
            ->add('section', EntityType::class, [
                'class'         => LessonSection::class,
                'query_builder' => function (EntityRepository $entityRepository) use ($courseId) {
                    return $entityRepository->createQueryBuilder('ls')
                        ->where('ls.course = :courseId')
                        ->setParameter('courseId', $courseId)
                        ->orderBy('ls.title');
                },
                'label'         => 'Раздел',
//                'attr'          => ['required' => 'required'],
                'constraints'   => [
                    new NotBlank(['message' => 'Выберите или создайте раздел']),
                ],
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Lesson::class,
        ]);
        $resolver->setRequired(['courseId']);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'hb_adminbundle_lesson';
    }


}
