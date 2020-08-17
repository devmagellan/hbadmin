<?php

namespace HB\AdminBundle\Form\CustomerTransferWiseAccount;

use HB\AdminBundle\Entity\CustomerTransferWiseAccount;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class CustomerTransferWiseRussiaLocalType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('ruSurname', TextType::class, [
                'label'       => 'Фамилия',
                'required'    => true,
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('ruFirstName', TextType::class, [
                'label'       => 'Имя',
                'required'    => true,
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('ruPatronymic', TextType::class, [
                'label'       => 'Отчество',
                'required'    => true,
                'constraints' => [
                    new NotBlank(),
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
            ->add('ruLocalAccount', TextType::class, [
                'label'       => 'Номер счета',
                'required'    => true,
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 10]),
                    new Regex(['pattern' => '/^[0-9]+$/', 'message' => 'Разрешенные символы 0-9']),
                ],
            ])
            ->add('ruLocalBankCode', TextType::class, [
                'label'       => 'Бик (банковский код)',
                'required'    => true,
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 4]),
                    new Regex(['pattern' => '/^[0-9]+$/']),
                ],
            ])
            ->add('ruRegion', ChoiceType::class, [
                'label'    => 'Регион РФ',
                'required' => true,
                'choices'  => array_flip(RussiaRegionsHelper::REGIONS),
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
        return 'transferwise_account_russia_local';
    }
}
