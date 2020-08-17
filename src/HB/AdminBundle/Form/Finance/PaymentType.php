<?php

namespace HB\AdminBundle\Form\Finance;

use Doctrine\ORM\EntityRepository;
use HB\AdminBundle\Entity\Customer;
use HB\AdminBundle\Entity\CustomerPayment;
use HB\AdminBundle\Entity\CustomerRole;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class PaymentType extends AbstractType
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
            ->add('amount', NumberType::class, [
                'label'       => 'Сумма выплаты, $',
                'required'    => true,
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('details', TextType::class, [
                'label'    => 'Комментарий',
                'required' => false,
            ])
            ->add('paymentDate', DateType::class, [
                'label'       => 'Дата выплаты',
                'required'    => true,
                'constraints' => [
                    new NotBlank(),
                ],
                'widget'      => 'single_text',
                'format'      => 'yyyy-MM-dd',
                'html5'       => false,
                'attr'        => ['class' => 'datepicker'],
            ])
            ->add('startDate', DateType::class, [
                'label'       => 'Начало периода',
                'required'    => true,
                'constraints' => [
                    new NotBlank(),
                ],
                'widget'      => 'single_text',
                'format'      => 'yyyy-MM-dd',
                'html5'       => false,
                'attr'        => ['class' => 'datepicker'],
            ])
            ->add('endDate', DateType::class, [
                'label'       => 'Конец периода',
                'required'    => true,
                'constraints' => [
                    new NotBlank(),
                ],
                'widget'      => 'single_text',
                'format'      => 'yyyy-MM-dd',
                'html5'       => false,
                'attr'        => ['class' => 'datepicker'],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CustomerPayment::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'payment_add';
    }
}
