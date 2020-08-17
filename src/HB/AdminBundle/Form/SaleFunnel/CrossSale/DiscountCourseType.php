<?php

namespace HB\AdminBundle\Form\SaleFunnel\CrossSale;

use Doctrine\ORM\EntityRepository;
use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Entity\SaleFunnel\CrossSale\DiscountCourse;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelCrossSale;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DiscountCourseType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Course $course */
        $course = $options['course'];

        $discounts = SalesFunnelCrossSale::getPossibleDiscounts();
        $choices = [];
        foreach ($discounts as $discount) {
            $choices[$discount.' %'] = $discount;
        }

        $builder
            ->add('course', EntityType::class, [
                'label'         => 'Продукт (до 50% скидки на этот продукт)',
                'class'         => Course::class,
                'query_builder' => function (EntityRepository $entityRepository) use ($course) {

                    $takesPartInAuthorClub = (bool) $course->getSalesFunnelAuthorClub();

                    $query = $entityRepository->createQueryBuilder('c')
                        ->select('course')
                        ->from(Course::class, 'course')
                        ->leftJoin('course.owner', 'owner')
                        ->leftJoin('course.salesFunnelAuthorClub', 'sales_funnel_author_club')
                        ->where('course.id != :courseId AND owner = :owner');

                    if ($takesPartInAuthorClub) {
                        $query
                            ->orWhere('course.id != :courseId AND sales_funnel_author_club.id IS NOT NULL');
                    }

                    $query
                        ->setParameters([
                            'courseId' => $course->getId(),
                            'owner'    => $course->getOwner(),
                        ]);

                    return $query;
                },
                'required'      => true,
            ])
            ->add('discountPercent', ChoiceType::class, [
                'label'   => 'Скидка на курс',
                'choices' => $choices,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => DiscountCourse::class,
        ))->setRequired('course');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'hb_adminbundle_sale_funnel_cross_sale_discount_course';
    }
}
