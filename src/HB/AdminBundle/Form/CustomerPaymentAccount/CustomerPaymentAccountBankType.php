<?php

namespace HB\AdminBundle\Form\CustomerPaymentAccount;

use HB\AdminBundle\Entity\CustomerPaymentAccount;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerPaymentAccountBankType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('bankCustomerName', TextType::class, [
                'label'    => 'Наименование (для физического лица – фамилия, имя) и адрес (резидентство) получателя',
                'required' => false,
            ])
            ->add('bankName', TextType::class, [
                'label'    => 'Наименование банка-получателя',
                'required' => false,
            ])
            ->add('bankAddress', TextType::class, [
                'label'    => 'Адрес банка-получателя',
                'required' => false,
            ])
            ->add('bankSwiftCode', TextType::class, [
                'label'    => 'SWIFT-код банка-получателя',
                'required' => false,
            ])
            ->add('bankAccountCode', TextType::class, [
                'label'    => 'Номер счета В Долларах получателя',
                'required' => false,
            ])
            ->add('bankCorrespondent', TextType::class, [
                'label'    => 'Банк корреспондент',
                'required' => false,
            ]);
    }

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
        return 'hb_payment_account_paypal';
    }
}
