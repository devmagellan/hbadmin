<?php

namespace HB\AdminBundle\Form\SaleFunnel\Webinar;

use HB\AdminBundle\Entity\SaleFunnel\Webinar\WebinarThesis;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class WebinarThesisType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description', TextType::class, [
                'label'       => 'Что будет на вебинаре? (Программа вебинара)',
                'required'    => true,
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 20, 'max' => 200]),
                ],
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => WebinarThesis::class,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'hb_adminbundle_sale_funnel_webinar_thesis';
    }
}
