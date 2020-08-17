<?php

namespace HB\AdminBundle\Form\SaleFunnel\HotLeads;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class SuccessHistoryEditType extends SuccessHistoryType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('description', CKEditorType::class, [
                'label'       => 'Содержание',
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
    public function getBlockPrefix()
    {
        return 'hb_adminbundle_sale_funnel_hot_leads_success_history_edit';
    }
}
