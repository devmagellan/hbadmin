<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Service\Intercom;


final class IntercomAttributes
{
    public const MONTHS_SINCE_SIGNUP = 'Months since Signup';
    public const DAYS_SINCE_SIGNUP = 'Days since Signup';
    public const PSEUDO_NAME = 'Pseudo Name';
    public const PSEUDO_SURNAME = 'Pseudo Surname';
    public const ACCOUNT_TYPE = 'Account Type';
    public const RATE_PLAN = 'Rate Plan';
    public const EMAIL = "Email";
    public const NOT_EMPTY_LESSONS_COUNT = "Lessons Created";
    public const PUBLISHED_PRODUCTS = 'Published Products';
    public const TEAM_SIZE = 'Team Size';
    public const PROFILE_COMPLETED = 'Profile Completed';
    public const FUNNELS_ACTIVATED = 'Funnels Activated';

    public const TOTAL_SALES = 'Total Sales';
    public const TOTAL_STUDENTS = 'Total Students';

    public const PREVIOUS_SALES = 'Previous Sales';
    public const PREVIOUS_PRODUCT = 'Previous Product';
    public const SOURCE = 'Source';
    public const TEACHABLE_COURSE_COMPLETED = 'Online Course Completed';
    public const TEACHABLE_COURSE_ENROLLED = 'Online Course Enrolled';
    public const YOUCANBOOKME_BOOKED = 'Personal Demo';
    public const WEBINAR_ATTENDED = 'Webinar Attendance';

    /** Default attributes  */
    public const PHONE = "phone";
}