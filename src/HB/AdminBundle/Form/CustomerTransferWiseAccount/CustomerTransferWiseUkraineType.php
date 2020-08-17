<?php

namespace HB\AdminBundle\Form\CustomerTransferWiseAccount;

use HB\AdminBundle\Entity\CustomerTransferWiseAccount;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class CustomerTransferWiseUkraineType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('uaFullName', TextType::class, [
                'label'       => 'Полное Имя получателя (транслитом как в загран паспорте, или как в реквизитах в приватбанке)',
                'required'    => true,
                'constraints' => [
                    new NotBlank(),
                    new Regex(['pattern' => '/^[a-zA-Z -]+$/', 'message' => 'Используйте латинские буквы, пробел или дефис']),
                ],
            ])
            ->add('city', TextType::class, [
                'label'       => 'Город',
                'required'    => true,
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('address', TextType::class, [
                'label'       => 'Адрес',
                'required'    => true,
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('zip', TextType::class, [
                'label'       => 'Почтовый индекс',
                'required'    => true,
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('uaPhone', TextType::class, [
                'label'       => 'Номер телефона зарегистрированный в ПриватБанке',
                'required'    => true,
                'constraints' => [
                    new NotBlank(),
                    new Regex(['pattern' => '/^[+]*[0-9]*$/', 'message' => 'Разрешенные символы +, 0-9']),
                ],
            ])
            ->add('uaLastFourDigits', TextType::class, [
                'label'       => 'Последние 4 цифры карты Приватбанка',
                'required'    => true,
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 4, 'max' => 4]),
                    new Regex(['pattern' => '/^[0-9]+$/']),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CustomerTransferWiseAccount::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'transferwise_account_ukraine';
    }
}
