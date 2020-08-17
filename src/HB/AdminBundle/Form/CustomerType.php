<?php

namespace HB\AdminBundle\Form;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use HB\AdminBundle\Entity\Customer;
use HB\AdminBundle\Entity\CustomerRole;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CustomerType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $rolesQuery = $options['roles_query'];
        $authorsQuery = $options['authors_query'];
        $canChoosePacket = $options['can_choose_packet'];

        $builder
            ->add('email', EmailType::class, [
                'label'       => 'Email',
                'required'    => true,
                'constraints' => [
                    new Email(),
                    new NotBlank(),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type'           => PasswordType::class,
                'options'        => ['attr' => ['class' => 'password-field']],
                'required'       => true,
                'first_options'  => ['label' => 'Пароль'],
                'second_options' => ['label' => 'Повторить пароль'],
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
                'label'       => 'Телефон',
                'required'    => false,
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('aboutInfo', CKEditorType::class, [
                'config'      => ['toolbar' => 'basic'],
                'label'       => 'Информация о пользователе',
                'constraints' => [
                    new Length(['max' => 700]),
                ],
            ])
            ->add('photo_cdn', TextType::class, [
                'attr'     => ['style' => 'display: none'],
                'mapped'   => false,
                'required' => false,
            ])
            ->add('photo_uuid', TextType::class, [
                'attr'     => ['style' => 'display: none'],
                'mapped'   => false,
                'required' => false,
            ])
            ->add('photo_name', TextType::class, [
                'attr'     => ['style' => 'display: none'],
                'mapped'   => false,
                'required' => false,
            ])
            ->add('role', EntityType::class, [
                'class'         => CustomerRole::class,
                'query_builder' => $rolesQuery,
                'label'         => 'Роль',
            ]);

        if ($canChoosePacket) {
            $builder
                ->add('packet', null, [
                    'label' => 'Пакет',
                ]);
        }


        if ($authorsQuery) {
            $builder->add('authors', EntityType::class, [
                'class'         => Customer::class,
                'label'         => 'Закрепленные авторы',
                'query_builder' => $authorsQuery,
                'expanded'      => true,
                'multiple'      => true,
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Customer::class,
        ])
            ->setRequired('roles_query')
            ->setRequired('authors_query')
            ->setRequired('can_choose_packet')
            ->setRequired('packet');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'hb_adminbundle_customer';
    }


}
