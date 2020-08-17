<?php

namespace HB\AdminBundle\Form;

use HB\AdminBundle\Entity\Embedded\Integrations;
use HB\AdminBundle\Entity\Types\CustomerPacketType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IntegrationsType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $owner_packet = $options['owner_packet'];


        if ($owner_packet === CustomerPacketType::PROFESSIONAL) {
            $attr = ['placeholder' => 'Доступно на пакете выше', 'readonly' => true];
            $mapped = false;
        } else {
            $attr = [];
            $mapped = true;
        }

        $builder
            ->add('googleAnalytics', TextType::class, [
                'label'    => 'Google Analytics',
                'required' => false,
            ])
            ->add('facebookPixel', TextType::class, [
                'label'    => 'Facebook Pixel',
                'required' => false,
            ])
            ->add('yandexMetrics', TextType::class, [
                'label'    => 'Яндекс Метрика',
                'required' => false,
                'attr'     => $attr,
                'mapped'   => $mapped,
            ])
            ->add('vk', TextType::class, [
                'label'    => 'Вконтакте',
                'required' => false,
                'attr'     => $attr,
                'mapped'   => $mapped,
            ])
            ->add('ok', TextType::class, [
                'label'    => 'Одноклассники',
                'required' => false,
                'attr'     => $attr,
                'mapped'   => $mapped,
            ])
            ->add('twitter', TextType::class, [
                'label'    => 'Twitter',
                'required' => false,
                'attr'     => $attr,
                'mapped'   => $mapped,
            ])
            ->add('linkedin', TextType::class, [
                'label'    => 'Linkedin',
                'required' => false,
                'attr'     => $attr,
                'mapped'   => $mapped,
            ])
            ->add('pinterest', TextType::class, [
                'label'    => 'Pinterest',
                'required' => false,
                'attr'     => $attr,
                'mapped'   => $mapped,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Integrations::class,
            'method'     => 'POST',
        ))
            ->setRequired('owner_packet');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'hb_adminbundle_course_integrations';
    }


}
