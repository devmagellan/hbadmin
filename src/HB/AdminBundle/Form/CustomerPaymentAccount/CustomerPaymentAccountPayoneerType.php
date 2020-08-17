<?php

namespace HB\AdminBundle\Form\CustomerPaymentAccount;

use HB\AdminBundle\Entity\CustomerPaymentAccount;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerPaymentAccountPayoneerType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('payoneerEmail', EmailType::class, [
                'label'    => 'Email, пользователя Payoneer, нужна регистрация в системе (комиссия 1%)',
                'required' => false,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => CustomerPaymentAccount::class,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'hb_payment_account_payoneer';
    }
}
