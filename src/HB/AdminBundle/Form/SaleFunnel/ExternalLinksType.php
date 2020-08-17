<?php

namespace HB\AdminBundle\Form\SaleFunnel;

use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelDownSell;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelWebinar;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ExternalLinksType extends AbstractType
{
    public const ONLINE_SCHOOL_FIELD = 'online_school';
    public const PAYMENT_PAGE_FIELD = 'payment_page';
    public const ORGANIC_FIELD = 'organic';
    public const WEBINAR_FIELD = 'webinar_';
    public const DOWNSELL_FIELD = 'downsell_';
    public const EDUCATIONAL = 'educational';

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Course $course */
        $course = $options['course'];
        $isAdmin = $options['isAdmin'];
        $attrReadonly = $isAdmin ? [] : ['readonly' => true];

        $builder->add(self::ONLINE_SCHOOL_FIELD, TextType::class, [
            'label'       => 'Онлайн Школа',
            'required'    => true,
            'data'        => $course->getLinkOnlineSchool(),
            'constraints' => [
                new NotBlank(),
            ],
            'attr'        => $attrReadonly,
        ]);

        $builder->add(self::PAYMENT_PAGE_FIELD, TextType::class, [
            'label'       => 'Страница Оплаты',
            'required'    => true,
            'data'        => $course->getLinkPaymentPage(),
            'constraints' => [
                new NotBlank(),
            ],
            'attr'        => $attrReadonly,
        ]);

        if ($course->getSalesFunnelOrganic()) {
            $builder->add(self::ORGANIC_FIELD, TextType::class, [
                'label'       => 'Органическая Целевая Страница',
//                'required'    => true,
                'data'        => $course->getSalesFunnelOrganic()->getExternalLink(),
                /*'constraints' => [
                    new NotBlank(),
                ],*/
                'attr'        => $attrReadonly,
            ]);
        }

        if ($course->getSalesFunnelDownSells()->count() > 0) {

            /** @var SalesFunnelDownSell $downSell */
            foreach ($course->getSalesFunnelDownSells() as $downSell) {
                $builder->add(self::DOWNSELL_FIELD.$downSell->getId(), TextType::class, [
                    'label'       => sprintf('Целевая Страница Выгодной Формулы #%s', $downSell->getId()),
//                    'required'    => true,
                    'data'        => $downSell->getExternalLink(),
                    /*'constraints' => [
                        new NotBlank(),
                    ],*/
                    'attr'        => $attrReadonly,
                ]);
            }
        }

        if ($course->getSalesFunnelWebinar()->count() > 0) {

            /** @var SalesFunnelWebinar $webinar */
            foreach ($course->getSalesFunnelWebinar() as $webinar) {
                $builder->add(self::WEBINAR_FIELD.$webinar->getId(), TextType::class, [
                    'label'       => sprintf('Страница Регистрации на Вебинар #%s %s', $webinar->getId(), $webinar->getTitle()),
//                    'required'    => true,
                    'data'        => $webinar->getExternalLink(),
                    /*'constraints' => [
                        new NotBlank(),
                    ],*/
                    'attr'        => $attrReadonly,
                ]);
            }
        }

        if ($course->getSalesFunnelEducational()) {
            $builder->add(self::EDUCATIONAL, TextType::class, [
                'label'       => 'Ваши Статьи в Блоге',
//                'required'    => true,
                'data'        => $course->getSalesFunnelEducational()->getExternalLink(),
                /*'constraints' => [
                    new NotBlank(),
                ],*/
                'attr'        => $attrReadonly,
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(['method' => 'POST',])
            ->setRequired('course')
            ->setRequired('isAdmin');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'hb_course_external_links';
    }
}
