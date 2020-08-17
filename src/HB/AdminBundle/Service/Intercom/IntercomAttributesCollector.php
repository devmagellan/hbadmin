<?php

declare(strict_types = 1);

namespace HB\AdminBundle\Service\Intercom;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Entity\Customer;
use HB\AdminBundle\Entity\CustomerPaymentAccount;
use HB\AdminBundle\Entity\Lesson;
use HB\AdminBundle\Entity\Teachable\TeachableCourseStudent;
use HB\AdminBundle\Entity\Teachable\TeachableTransaction;
use HB\AdminBundle\Entity\Types\CourseStatusType;
use HB\AdminBundle\Entity\Types\CustomerPacketType;
use HB\AdminBundle\Service\ZapierEventCollector;
use HB\AdminBundle\Validator\PaymentAccountValidator;
use HB\AdminBundle\Validator\SaleFunnelLayerCakeValidator;
use HB\AdminBundle\Validator\SaleFunnelOneTimeOfferValidator;
use HB\AdminBundle\Validator\SaleFunnelOrganicValidator;

class IntercomAttributesCollector
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var IntercomClient
     */
    private $intercomClient;

    /**
     * @var ZapierEventCollector
     */
    private $zapierEventCollector;

    /**
     * IntercomAttributesCollector constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param IntercomClient         $intercomClient
     * @param ZapierEventCollector   $zapierEventCollector
     */
    public function __construct(EntityManagerInterface $entityManager, IntercomClient $intercomClient, ZapierEventCollector $zapierEventCollector)
    {
        $this->entityManager = $entityManager;
        $this->intercomClient = $intercomClient;
        $this->zapierEventCollector = $zapierEventCollector;
    }

    /**
     * @param Customer $customer
     */
    public function updateCustomerAttributes(Customer $customer): void
    {
        $email = $customer->getValidEmail();
        $profileCompleted = $this->isProfileCompleted($customer);

        if ($profileCompleted) {
            $this->zapierEventCollector->profileCompleted($customer);
        }

        $this->intercomClient->getClient()->users->update([
            "email"             => $email->getValue(),
            "name"              => $customer->getFirstName().' '.$customer->getSurname(),
            "phone"             => $customer->getPhone(),
            // "user_id"           => $customer->getId(),
            "user_id"           => $email->getValue(),
            "custom_attributes" => [
                IntercomAttributes::MONTHS_SINCE_SIGNUP     => $this->collectMonthSinceSignUp($customer),
                IntercomAttributes::DAYS_SINCE_SIGNUP       => $this->collectDaysSinceSignUp($customer),
                IntercomAttributes::PSEUDO_NAME             => $customer->getFirstName(),
                IntercomAttributes::PSEUDO_SURNAME          => $customer->getSurname(),
                IntercomAttributes::ACCOUNT_TYPE            => ucfirst(strtolower(str_replace('ROLE_', '', $customer->getRole()->getName()))),
                IntercomAttributes::RATE_PLAN               => $this->collectRatePlan($customer),
                IntercomAttributes::EMAIL                   => $email->getValue(),
                IntercomAttributes::NOT_EMPTY_LESSONS_COUNT => $this->collectNotEmptyLessons($customer),
                IntercomAttributes::PUBLISHED_PRODUCTS      => $this->collectPublishedProducts($customer),
                IntercomAttributes::TEAM_SIZE               => $this->collectTeamSize($customer),
                IntercomAttributes::PROFILE_COMPLETED       => $profileCompleted,
                IntercomAttributes::FUNNELS_ACTIVATED       => $this->collectActiveFunnels($customer),
                IntercomAttributes::TOTAL_SALES             => $this->collectTotalSales($customer),
                IntercomAttributes::TOTAL_STUDENTS          => $this->collectTotalStudents($customer),
            ],
        ]);
    }

    /**
     * @param Customer $customer
     *
     * @return int
     */
    private function collectTeamSize(Customer $customer): int
    {
        $teamSize = 1;
        $highestMember = $customer;

        if ($customer->getOwner() && !$customer->getOwner()->isAdmin()) {

            $highestMember = $customer->getOwner();

            if ($highestMember->getOwner() && !$highestMember->getOwner()->isAdmin()) {
                $highestMember = $highestMember->getOwner();
            }
        }

        /** @var Customer $firstLevelCustomer */
        foreach ($highestMember->getCustomers() as $firstLevelCustomer) {
            $teamSize++;

            $teamSize += $firstLevelCustomer->getCustomers()->count();
        }

        return $teamSize;
    }

    /**
     * @param Customer $customer
     *
     * @return int
     */
    private function collectMonthSinceSignUp(Customer $customer)
    {
        $diff = $customer->getDateCreatedAt()->diff(new \DateTime());

        return $diff->m;
    }

    /**
     * @param Customer $customer
     *
     * @return int
     */
    private function collectDaysSinceSignUp(Customer $customer)
    {
        $diff = $customer->getDateCreatedAt()->diff(new \DateTime());

        return $diff->days;
    }

    /**
     * @param Customer $customer
     *
     * @return string
     */
    private function collectRatePlan(Customer $customer): string
    {
        $plan = $customer->getPacket();
        $answer = 'Premium';

        if ($plan) {
            $value = $plan->getType();

            switch ($value) {
                case CustomerPacketType::PREMIUM:
                    $answer = 'Premium';
                    break;
                case CustomerPacketType::PROFESSIONAL:
                    $answer = 'Pro';
                    break;
                case CustomerPacketType::CUSTOM:
                    $answer = 'Ind';
                    break;
                case CustomerPacketType::VIP:
                    $answer = 'VIP';
                    break;
                case CustomerPacketType::WEBINAR:
                    $answer = 'Webinar';
                    break;
                case CustomerPacketType::ONLINE_SCHOOL:
                    $answer = 'Shkola';
                    break;
            }

            return $answer;
        }
    }

    /**
     * @param Customer $customer
     *
     * @return int
     */
    private function collectNotEmptyLessons(Customer $customer): int
    {
        $res = $this->entityManager->createQueryBuilder()
            ->select('lesson.id, COUNT(elements.id) as content_count')
            ->from(Lesson::class, 'lesson')
            ->leftJoin('lesson.section', 'lesson_sections')
            ->leftJoin('lesson_sections.course', 'course')
            ->leftJoin('lesson.elements', 'elements')
            ->groupBy('lesson')
            ->where('course.owner = :customer')
            ->andWhere('course.sandbox = :not_sandBox')
            ->setParameter('not_sandBox', false)
            ->having('COUNT(elements.id) >= 1')
            ->setParameter('customer', $customer)
            ->getQuery()->getResult();

        return \count($res);
    }

    /**
     * @param Customer $customer
     */
    private function collectPublishedProducts(Customer $customer)
    {
        return (int) $this->entityManager->createQueryBuilder()
            ->select('COUNT(course.id)')
            ->from(Course::class, 'course')
            ->where('course.owner = :customer')
            ->andWhere('course.status = :published')
            ->setParameters([
                'customer'  => $customer,
                'published' => CourseStatusType::STATUS_PUBLISHED,
            ])
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Check if customer profile completed
     * Min requirements: one of payment accout, about info, photo
     *
     * @param Customer $customer
     *
     * @return bool
     */
    private function isProfileCompleted(Customer $customer): bool
    {
        if (!$customer->getPaymentAccount()) {
            $this->fallbackEmptyPaymentAccount($customer);
        }

        $paymentValid = PaymentAccountValidator::isValid($customer->getPaymentAccount(), $customer);

        if ($paymentValid && $customer->getPhoto() && $customer->getAboutInfo()) {
            return true;
        }

        return false;
    }

    /**
     * Temporary fallback method to create customer payment account
     *
     * @param Customer $customer
     */
    private function fallbackEmptyPaymentAccount(Customer $customer)
    {
        $paymentAccount = new CustomerPaymentAccount($customer);
        $customer->setPaymentAccount($paymentAccount);

        $this->entityManager->persist($paymentAccount);
        $this->entityManager->persist($customer);
        $this->entityManager->flush();
    }

    /**
     * Collect customer course active funnels
     *
     * @param Customer $customer
     *
     * @return int
     */
    private function collectActiveFunnels(Customer $customer): int
    {
        $activeFunnels = 0;
        $courses = $this->entityManager->getRepository(Course::class)->findBy(['owner' => $customer, 'sandbox' => false]);

        foreach ($courses as $course) {
            $activeFunnels += $this->countActiveFunnels($course);
        }

        return $activeFunnels;
    }

    /**
     * @param Course $course
     *
     * @return int
     */
    private function countActiveFunnels(Course $course): int
    {
        $activeFunnels = 0;

        if ($course->getSalesFunnelOrganic() && empty(SaleFunnelOrganicValidator::validate($course->getSalesFunnelOrganic()))) {
            $activeFunnels++;
        }

        $activeFunnels += $course->getSaleFunnelLeadCollections()->count();

        if ($course->getSalesFunnelBrokenBasket()) {
            $activeFunnels++;
        }

        if ($course->getSalesFunnelPostSale()) {
            $activeFunnels++;
        }

        if ($course->getSalesFunnelCrossSale()) {
            $activeFunnels++;
        }

        if ($course->getSalesFunnelHotLeads()) {
            $activeFunnels++;
        }

        foreach ($course->getSaleFunnelLayerCakes() as $cake) {
            if (empty(SaleFunnelLayerCakeValidator::validate($cake))) {
                $activeFunnels++;
            }
        }

        if ($course->getSalesFunnelMiniCourse()) {
            $activeFunnels++;
        }

        $activeFunnels += $course->getSalesFunnelWebinar()->count();

        $activeFunnels += $course->getSalesFunnelDownSells()->count();

        if ($course->getSalesFunnelWalkerStart()) {
            $activeFunnels++;
        }

        if ($course->getSalesFunnelAuthorClub()) {
            $activeFunnels++;
        }

        if ($course->getSalesFunnelEducational()) {
            $activeFunnels++;
        }

        foreach ($course->getSalesFunnelOneTimeOffer() as $offer) {
            if (empty(SaleFunnelOneTimeOfferValidator::validate($offer))) {
                $activeFunnels++;
            }
        }

        $activeFunnels += $course->getSaleFunnelPartners()->count();

        return $activeFunnels;

    }

    /**
     * @param Customer $customer
     *
     * @return float
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function collectTotalSales(Customer $customer): float
    {
        if ($customer->getProducer()) { // don't count sales for author with producer
            $totalSales = 0.0;
        } else {
            $ids = $this->getCoursesIds($customer);

            $sales = $this->entityManager->createQueryBuilder()
                ->select('SUM(tr.finalPrice) as sum')
                ->from(TeachableTransaction::class, 'tr')
                ->innerJoin(Course::class, 'course', Join::WITH, 'tr.course_id = course.teachableId')
                ->where('course.id in (:ids)')->setParameter('ids', $ids)
                ->getQuery()->getSingleScalarResult();



            $refunds = $this->entityManager->createQueryBuilder()
                ->select('SUM(tr.refundAmount) as sum')
                ->from(TeachableTransaction::class, 'tr')
                ->innerJoin(Course::class, 'course', Join::WITH, 'tr.course_id = course.teachableId')
                ->where('course.id in (:ids)')->setParameter('ids', $ids)
                ->getQuery()->getSingleScalarResult();

            $totalSales = round(($sales - $refunds)/100, 2);
        }

        return $totalSales;
    }

    /**
     * @param Customer $customer
     *
     * @return int
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function collectTotalStudents(Customer $customer)
    {
        if ($customer->getProducer()) { // don't count students for author with producer
            $students = 0;
        } else {
            $ids = $this->getCoursesIds($customer);

            $students = (int) $this->entityManager->createQueryBuilder()
                ->select('COUNT(DISTINCT(student.studentEmail)) as studentsCount')
                ->from(TeachableCourseStudent::class, 'student')
                ->innerJoin(Course::class, 'course', Join::WITH, 'student.course_id = course.teachableId')
                ->where('course.id in (:ids)')->setParameter('ids', $ids)
                ->getQuery()->getSingleScalarResult();
        }

        return $students;
    }

    /**
     * @param Customer $customer
     *
     * @return array
     */
    private function getCoursesIds(Customer $customer): array
    {
        $coursesQuery = $this->entityManager->getRepository(Course::class)->getCoursesQuery($customer);
        $courses = $coursesQuery->getResult();

        $ids = array_map(function ($course) {
            /** @var Course $course */
            return $course->getId();
        }, $courses);

        return $ids;
    }

}