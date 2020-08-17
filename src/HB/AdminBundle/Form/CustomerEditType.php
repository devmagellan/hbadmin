<?php

namespace HB\AdminBundle\Form;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use HB\AdminBundle\Entity\Customer;
use HB\AdminBundle\Entity\CustomerRole;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CustomerEditType extends AbstractType
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
            ->add('plainPassword', RepeatedType::class, [
                'type'           => PasswordType::class,
                'options'        => ['attr' => ['class' => 'password-field']],
                'required'       => false,
                'first_options'  => ['label' => 'Пароль'],
                'second_options' => ['label' => 'Повторить пароль'],
            ])
            ->add('firstName', TextType::class, [
                'label'       => 'Имя',
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('surname', TextType::class, [
                'label'       => 'Фамилия',
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('aboutInfo', CKEditorType::class, [
                'label'       => 'Информация о себе',
                'config'      => ['toolbar' => 'basic'],
                'constraints' => [
                    new Length(['max' => 700]),
                ],
            ])
            ->add('role', EntityType::class, [
                'class'         => CustomerRole::class,
                'query_builder' => $rolesQuery,
                'label'         => 'Роль',
            ])
            ->add('packetUntilDate', DateType::class, [
                'label'    => 'Оплачено до',
                'widget'   => 'single_text',
                'required' => false,
                'html5'    => false,
                'attr'     => ['class' => 'datepicker'],
            ])
        ;

        if ($canChoosePacket) {
            $builder
                ->add('packet', null, [
                    'label' => 'Пакет',
                ])
                ->add('packetSubscription', ChoiceType::class, [
                    'label'   => 'Подписка',
                    'choices' => array_flip(Customer::PACKET_SUBSCRIPTIONS),
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
        $resolver
            ->setDefaults(['data_class' => Customer::class])
            ->setRequired('roles_query')
            ->setRequired('authors_query')
            ->setRequired('can_choose_packet');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'hb_adminbundle_customer_edit';
    }


}
