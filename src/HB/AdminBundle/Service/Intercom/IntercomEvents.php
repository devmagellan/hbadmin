<?php

declare(strict_types = 1);

namespace HB\AdminBundle\Service\Intercom;

final class IntercomEvents
{
    public const CREATE_LESSON = 'create lesson';
    public const ACCESS_LESSON = 'access lesson';
    public const ACCESS_REPORTS = 'access reports';
    public const ACCESS_PROFILE = 'access profile';

    public const ACCESS_FUNNEL_ORGANIC = 'access organic funnel';
    public const ACCESS_FUNNEL_LEADS = 'access leads funnel';
    public const ACCESS_FUNNEL_WEBINAR = 'access webinar funnel ';
    public const ACCESS_FUNNEL_FORMULA = 'access formula funnel';
    public const ACCESS_FUNNEL_OTO = 'access OTO funnel';
    public const ACCESS_FUNNEL_EDUCATIONAL = 'access educational funnel';
    public const ACCESS_FUNNEL_CLUB = 'access club funnel';
    public const ACCESS_FUNNEL_PARTNER = 'access referral funnel';
    public const ACCESS_FUNNEL_POST_SALE = 'access upsell funnel';
    public const ACCESS_FUNNEL_CROSS_SALE = 'access cross funnel';
    public const ACCESS_FUNNEL_CART_SALE = 'access cart funnel';
    public const ACCESS_FUNNEL_HOT_LEADS = 'access hot leads funnel';
    public const ACCESS_FUNNEL_SLICED = 'access sliced funnel';

    public const ACCESS_FUNNEL_QUEUE = 'access funnels queue';
    public const ACCESS_DASHBOARD = 'access dashboard';

    public const SEND_PRODUCT_TO_MODERATION = 'send product to moderation';
    public const ADD_IMAGE = 'add image';
    public const ADD_FILE = 'add file';

    public const ACCESS_EXCEPTION = 'access exception';
    public const CRITICAL_EXCEPTION = 'critical exception';
    public const COURSE_ADD_TEXT = 'add text';

}