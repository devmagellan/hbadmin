<?php

namespace HB\AdminBundle\Form\PriceBlockSetting;


class SettingWithCoursesVIPType extends SettingWithCoursesOptimalType
{
    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'hb_adminbundle_course_price_block_setting_with_courses_vip';
    }


}
