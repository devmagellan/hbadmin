<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Form\Customer;


use HB\AdminBundle\Entity\Customer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class ConfirmSignUpDataType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('plainPassword', RepeatedType::class, [
                'type'            => PasswordType::class,
                'options'         => ['attr' => ['class' => 'password-field']],
                'required'        => true,
                'first_options'   => [
                    'label'       => 'Пароль',
                    'constraints' => [
                        new Length(['min' => 8, 'minMessage' => 'Минимум 8 символов']),
                        new Regex(['pattern' => '/^[A-Za-z0-9]+$/', 'message' => 'Не используйте кириллицу']),
                    ],
                ],
                'second_options'  => [
                    'label'       => 'Повторить пароль',
                    'constraints' => [
                        new Length(['min' => 8, 'minMessage' => 'Минимум 8 символов']),
                        new Regex(['pattern' => '/^[A-Za-z0-9]+$/', 'message' => 'Не используйте кириллицу']),
                    ],
                ],
                'invalid_message' => 'Пароли должны совпадать',
            ])
            ->add('firstName', TextType::class, [
                'label'       => 'Имя',
                'required'    => true,
                'constraints' => [
                    new NotBlank(),

                ],
            ])
            ->add('surname', TextType::class, [
                'label'       => 'Фамилия',
                'required'    => true,
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('phone', TextType::class, [
                'label'       => 'Укажите телефон (придет смс-подтверждение)',
                'required'    => true,
                'constraints' => [
                    new Regex(['pattern' => '/^[+]*[0-9]*$/', 'message' => 'Разрешенные символы +, 0-9']),
                ],
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Customer::class,
            'method'     => 'POST',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'hb_confirm_data';
    }


}
