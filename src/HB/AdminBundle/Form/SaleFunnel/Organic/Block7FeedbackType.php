<?php

namespace HB\AdminBundle\Form\SaleFunnel\Organic;

use HB\AdminBundle\Entity\SaleFunnel\Organic\FeedBackVideo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class Block7FeedbackType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('feedBackAuthor', null, [
                'label'       => 'Автор отзыва',
                'required'    => true,
                'constraints' => [new NotBlank()],
            ])
            ->add('fileCdn', TextType::class, [
                'mapped' => false,
                'required' => false
            ])
            ->add('fileName', TextType::class, [
                'mapped' => false,
                'required' => false,
            ])
            ->add('fileUuid', TextType::class, [
                'mapped' => false,
                'required' => false
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => FeedBackVideo::class,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'hb_adminbundle_sale_funnel_organic_block7_feedback';
    }
}
