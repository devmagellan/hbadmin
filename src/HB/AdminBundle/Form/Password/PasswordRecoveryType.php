<?php

namespace HB\AdminBundle\Form\Password;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class PasswordRecoveryType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', RepeatedType::class, [
                'label'           => 'Укажите новый пароль',
                'invalid_message' => 'Пароли должны совпадать',
                'type'            => PasswordType::class,
                'first_options'   => ['label' => 'Новый пароль'],
                'second_options'  => ['label' => 'Повторите пароль'],
                'constraints'     => [
                    new NotBlank(),
                    new Length(['min' => 8]),
                ],
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'method' => 'POST',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'hb_password_recovery';
    }


}
