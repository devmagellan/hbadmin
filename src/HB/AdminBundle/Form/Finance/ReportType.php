<?php

namespace HB\AdminBundle\Form\Finance;

use Doctrine\ORM\EntityRepository;
use HB\AdminBundle\Entity\Customer;
use HB\AdminBundle\Entity\CustomerPaymentReport;
use HB\AdminBundle\Entity\CustomerRole;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

class ReportType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('customer', EntityType::class, [
                'class'         => Customer::class,
                'label'         => 'Пользователь',
                'required'      => true,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->leftJoin('c.role', 'role')
                        ->where('role.name = :role_author OR role.name = :role_producer')
                        ->setParameters([
                            'role_author'   => CustomerRole::ROLE_AUTHOR,
                            'role_producer' => CustomerRole::ROLE_PRODUCER,
                        ])
                        ->orderBy('c.surname');
                },
            ])
            ->add('reportMonth', ChoiceType::class, [
                'label'                     => 'Месяц',
                'required'                  => true,
                'choices'                   => [
                    'January'   => 1,
                    'February'  => 2,
                    'March'     => 3,
                    'April'     => 4,
                    'May'       => 5,
                    'June'      => 6,
                    'July'      => 7,
                    'August'    => 8,
                    'September' => 9,
                    'October'   => 10,
                    'November'  => 11,
                    'December'  => 12,
                ],
                'choice_translation_domain' => true,
                'translation_domain'        => 'messages',
                'constraints'               => [
                    new NotBlank(),
                    new Range(['min' => 1, 'max' => 12]),
                ],
            ])
            ->add('reportYear', ChoiceType::class, [
                'label'       => 'Год',
                'choices'     => [
                    2017 => 2017,
                    2018 => 2018,
                    2019 => 2019,
                    2020 => 2020,
                ],
                'constraints' => [
                    new NotBlank(),
                    new Range(['min' => 2017, 'max' => 2020]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CustomerPaymentReport::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'payment_report';
    }
}
