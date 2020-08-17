<?php

namespace HB\AdminBundle\Form\SaleFunnel\Educational;

use HB\AdminBundle\Entity\SaleFunnel\Educational\Letter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class LetterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('name', TextType::class, [
                'label'       => 'Название образовательного урока',
                'required'    => true,
                'constraints' => [new NotBlank()],
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Letter::class,
            'method'     => 'POST',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'hb_adminbundle_sale_funnel_educational_letter';
    }
}
