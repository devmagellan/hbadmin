<?php

namespace HB\AdminBundle\Form\SaleFunnel\HotLeads;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use HB\AdminBundle\Entity\SaleFunnel\HotLeads\SuccessHistory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class SuccessHistoryType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('description', CKEditorType::class, [
                'label'       => 'Добавьте историю успеха (Если вы не дадите истории успеха, то мы вышлем в этих сериях писем ему что-то)',
                'required'    => false,
                'config'      => ['toolbar' => 'basic'],
                'constraints' => [
                    new NotBlank(),
                ],
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => SuccessHistory::class,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'hb_adminbundle_sale_funnel_hot_leads_success_history';
    }
}
