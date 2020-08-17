<?php

namespace HB\AdminBundle\Form;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use HB\AdminBundle\Entity\Customer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class CustomerSelfEditType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('plainPassword', RepeatedType::class, [
                'type'           => PasswordType::class,
                'options'        => array('attr' => array('class' => 'password-field')),
                'required'       => false,
                'first_options'  => array('label' => 'Пароль'),
                'second_options' => array('label' => 'Повторить пароль'),
            ])
            ->add('firstName', null, [
                'label' => 'Имя',
            ])
            ->add('surname', null, [
                'label' => 'Фамилия',
            ])
            ->add('aboutInfo', CKEditorType::class, [
                'label'       => 'Информация о себе',
                'config'      => ['toolbar' => 'basic'],
                'constraints' => [
                    new Length(['max' => 700]),
                ],
            ]);

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(['data_class' => Customer::class]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'hb_adminbundle_customer_edit';
    }


}
