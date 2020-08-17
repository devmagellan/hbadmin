<?php

namespace HB\AdminBundle\Form\SaleFunnel\DownSell;

use Doctrine\ORM\EntityRepository;
use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Entity\LessonSection;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelDownSell;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

class DownSellType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Course $course */
        $course = $options['course'];

        $builder
            ->add('price', NumberType::class, [
                'label'       => 'Цена',
                'required'    => true,
                'constraints' => [
                    new NotBlank(),
                    new GreaterThanOrEqual(['value' => 0]),
                ],
            ])
            ->add('term', NumberType::class, [
                'label'       => 'Срок предоставления (дней)',
                'constraints' => [
                    new NotBlank(),
                    new Range(['min' => 1]),
                ],
            ])
            ->add('sections', EntityType::class, [
                'class'         => LessonSection::class,
                'label'         => 'Разделы',
                'query_builder' => function (EntityRepository $entityRepository) use ($course) {
                    return $entityRepository->createQueryBuilder('ls')
                        ->where('ls.course = :courseId')
                        ->setParameter('courseId', $course->getId())
                        ->orderBy('ls.title');
                },
                'expanded'      => true,
                'multiple'      => true,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => SalesFunnelDownSell::class,
            'method'     => 'POST',
        ));
        $resolver->setRequired('course');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'hb_adminbundle_sale_funnel_down_sell';
    }
}
