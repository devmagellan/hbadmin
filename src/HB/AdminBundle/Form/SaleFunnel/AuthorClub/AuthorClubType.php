<?php

namespace HB\AdminBundle\Form\SaleFunnel\AuthorClub;

use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelAuthorClub;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AuthorClubType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choices = [];
        for ($i = 0; $i <= 95; $i = $i + 5) {
            $choices[$i.' %'] = $i;
        }

        $builder
            ->add('discount', ChoiceType::class, [
                'label'    => 'Скидка',
                'required' => true,
                'choices'  => $choices,

            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SalesFunnelAuthorClub::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'author_club';
    }
}
