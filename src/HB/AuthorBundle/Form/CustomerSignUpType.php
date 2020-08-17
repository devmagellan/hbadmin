<?php

namespace HB\AuthorBundle\Form;

use HB\AdminBundle\Entity\Customer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class CustomerSignUpType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label'       => 'Email',
                'constraints' => [
                    new NotBlank(),
                    new Email(),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type'            => PasswordType::class,
                'options'         => array('attr' => array('class' => 'password-field')),
                'required'        => true,
                'first_options'   => array('label' => 'Пароль'),
                'second_options'  => array('label' => 'Повторить пароль'),
                'invalid_message' => 'Пароли должны совпадать!',
                'constraints'     => [
                    new NotBlank(),
                    new Length(['min' => 8]),
                ],
            ])
            ->add('firstName', TextType::class, [
                'label'       => 'Имя',
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 3]),
                ],
            ])
            ->add('surname', TextType::class, [
                'label'       => 'Фамилия',
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 2]),
                ],
            ])
            ->add('phone', TextType::class, [
                'label'       => 'Телефон',
                'constraints' => [
                    new NotBlank(),
                    new Regex([
                        'pattern' => "/^[0-9\/-]*$/",
                        'message' => 'Пожалуйста, используйте цифры 0-9, - или /',
                    ]),
                ],
            ])
            ->add('packetPlain', HiddenType::class, [
                'mapped' => false,
            ]);

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Customer::class,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'hb_author_signup';
    }


}
