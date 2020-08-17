<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Service;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Entity\CoursePriceBlock;
use HB\AdminBundle\Entity\Customer;
use HB\AdminBundle\Entity\Lesson;
use HB\AdminBundle\Entity\LessonElement;
use HB\AdminBundle\Entity\LessonSection;
use HB\AdminBundle\Entity\QuizQuestion;
use HB\AdminBundle\Entity\QuizQuestionAnswer;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelCrossSale;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelMiniCourse;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelOrganic;
use HB\AdminBundle\Entity\Types\LessonElementType;
use HB\AdminBundle\Exception\OnlyAuthorOrProducerCanHaveSandboxException;

class SandBoxCourseService
{
    private const COURSE_TITLE = 'Курс Песочница: Навигация в личном кабинете';
    private const COURSE_SUB_TITLE = 'Нажмите на кнопку Сохранить, чтобы продолжить';

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * SandBoxCourseService constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param Customer $customer
     *
     * @return Course
     *
     * @throws OnlyAuthorOrProducerCanHaveSandboxException
     */
    public function create(Customer $customer)
    {
        if ($customer->isAuthor() && $customer->isProducer() && $customer->isAdmin()) {
            throw new OnlyAuthorOrProducerCanHaveSandboxException(sprintf('Пользователь %s не может иметь учебного курса ', $customer->getId()));
        }

        return $this->createSandbox($customer);
    }

    /**
     * @param Customer $customer
     *
     * @return bool
     */
    public function hasSandBox(Customer $customer): bool
    {
        return (bool) $this->entityManager->createQueryBuilder()
            ->select('1')
            ->from(Course::class, 'course')
            ->where('course.owner = :owner')
            ->andWhere('course.sandbox = :sandbox')
            ->setParameters([
                'owner'   => $customer,
                'sandbox' => true,
            ])->getQuery()->getOneOrNullResult();
    }

    /**
     * @param Customer $customer
     *
     * @return Course|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getCustomerSandboxCourse(Customer $customer): ?Course
    {
        return $this->entityManager->createQueryBuilder()
            ->select('course')
            ->from(Course::class, 'course')
            ->where('course.owner = :owner')
            ->andWhere('course.sandbox = :sandbox')
            ->setParameters([
                'owner'   => $customer,
                'sandbox' => true,
            ])->getQuery()->getOneOrNullResult();
    }

    /**
     * @param Customer $customer
     *
     * @return Course
     */
    private function createSandbox(Customer $customer): Course
    {
        $sandBox = new Course(true);
        $sandBox->setOwner($customer);
        $sandBox->getInfo()->setTitle(self::COURSE_TITLE);
        $sandBox->getInfo()->setSubTitle(self::COURSE_SUB_TITLE);

        $sandBox->setSalesFunnelOrganic($this->createOrganicFunnel($sandBox));

        $this->addFirstSection($sandBox);
        $this->addSecondSection($sandBox);
        $this->addThirdSection($sandBox);
        $this->addFourthSection($sandBox);

        $this->createCrossSaleFunnel($sandBox);
        $this->createMiniCourseFunnel($sandBox);

        $this->entityManager->persist($sandBox);
        $this->entityManager->flush();

        return $sandBox;
    }

    /**
     * @param Course $course
     *
     * @return SalesFunnelOrganic
     */
    private function createOrganicFunnel(Course $course): SalesFunnelOrganic
    {
        $funnel = new SalesFunnelOrganic($course);

        $pricebBlocks = $this->entityManager->getRepository(CoursePriceBlock::class)->findAll();

        /** @var CoursePriceBlock $block */
        foreach ($pricebBlocks as $block) {
            $funnel->addPriceBlock($block);
        }

        return $funnel;
    }

    /**
     * @param Course $course
     */
    private function createMiniCourseFunnel(Course $course)
    {
        $funnel = new SalesFunnelMiniCourse($course);
        $course->setSalesFunnelMiniCourse($funnel);
        $this->entityManager->persist($funnel);
    }

    /**
     * @param Course $course
     */
    private function createCrossSaleFunnel(Course $course)
    {
        $funnel = new SalesFunnelCrossSale($course);
        $course->setSalesFunnelCrossSale($funnel);
        $this->entityManager->persist($funnel);
    }

    /**
     * @param Course $course
     */
    private function addFirstSection(Course $course)
    {
        $section = new LessonSection($course);

        $section->setPriority(0);
        $section->setTitle('Как загружать разделы и уроки?');

        $lesson1 = new Lesson();
        $lesson1->setSection($section);
        $lesson1->setPriority(0);
        $lesson1->setTitle('Перетаскивать можно все: уроки, разделы, блоки внутри урока. Наведите на крестик слева от названия, чтобы попробовать');
        $lesson1->setIsFree(false);
        $section->addLesson($lesson1);

        $lesson2 = new Lesson();
        $lesson2->setSection($section);
        $lesson2->setPriority(1);
        $lesson2->setTitle('А еще можно перетаскивать уроки между разделами. Попробуйте сейчас!');
        $lesson1->setIsFree(false);
        $section->addLesson($lesson2);

        $lesson3 = new Lesson();
        $lesson3->setSection($section);
        $lesson3->setPriority(2);
        $lesson3->setTitle('Каждый Урок может содержать любое количество типов контента в любом порядке. Откройте этот урок чтобы увидеть, нажав на зеленую иконку редактирования справа');
        $section->addLesson($lesson3);
        $this->fillContent($lesson3);

        $lesson4 = new Lesson();
        $lesson4->setSection($section);
        $lesson4->setPriority(3);
        $lesson4->setTitle('Нажмите на иконку "Глаз" справа, чтобы изменить его доступность для предварительного просмотра. Серый - недоступен, Синий - доступен');
        $section->addLesson($lesson4);

        $lesson5 = new Lesson();
        $lesson5->setSection($section);
        $lesson5->setPriority(4);
        $lesson5->setTitle('Нажмите название этого урока для того, чтобы его отредактировать');
        $lesson1->setIsFree(false);
        $section->addLesson($lesson5);

        $this->addPriceBlocksToSection($section);
        $course->addLessonSection($section);
    }

    /**
     * @param Course $course
     */
    private function addSecondSection(Course $course)
    {
        $section = new LessonSection($course);
        $section->setPriority(1);
        $section->setTitle('Пример Раздела с публикацией уроков по единому расписанию. Чтобы изменить, нажмите на зеленую иконку справа');
        $section->setType(LessonSection::TYPE_AFTER_DAYS);
        $section->setAfterDays(4);

        $this->addPriceBlocksToSection($section);
        $course->addLessonSection($section);
    }

    /**
     * @param Course $course
     */
    private function addThirdSection(Course $course)
    {
        $date = new \DateTime();
        $date->modify('+1 month');

        $section = new LessonSection($course);
        $section->setPriority(2);
        $section->setTitle('Пример Раздела с публикацией уроков по единому расписанию. Чтобы изменить, нажмите на зеленую иконку справа');
        $section->setType(LessonSection::TYPE_BY_DATE);
        $section->setByDate($date);

        $this->addPriceBlocksToSection($section);
        $course->addLessonSection($section);
    }

    /**
     * @param Course $course
     */
    private function addFourthSection(Course $course)
    {
        $section = new LessonSection($course);
        $section->setPriority(3);
        $section->setTitle('Пример Раздела, недоступной для Базового пакета. Нажмите на любой чекбокс справа, чтобы изменить');

        $this->addPriceBlocksToSection($section, true);
        $course->addLessonSection($section);
    }

    /**
     * @param LessonSection $section
     * @param bool          $excludeBasic
     */
    private function addPriceBlocksToSection(LessonSection $section, bool $excludeBasic = false)
    {
        $pricebBlocks = $this->entityManager->getRepository(CoursePriceBlock::class)->findAll();

        /** @var CoursePriceBlock $block */
        foreach ($pricebBlocks as $block) {
            if ($excludeBasic && $block->getType() === CoursePriceBlock::TYPE_BASIC) {

            } else {
                $section->addPriceBlock($block);
            }
        }
    }

    /**
     * Fill lesson content
     *
     * @param Lesson $lesson
     */
    private function fillContent(Lesson $lesson)
    {
        $elements = new ArrayCollection();
        $lessonElementConsultation = new LessonElement($lesson, new LessonElementType(LessonElementType::CONSULTATION));
        $lessonElementConsultation->setConsultationAt(new \DateTime('+1 month'));
        $lessonElementConsultation->setPriority(4);
        $elements->add($lessonElementConsultation);

        $lessonElementWebinar = new LessonElement($lesson, new LessonElementType(LessonElementType::WEBINAR));
        $lessonElementWebinar->setWebinarAt(new \DateTime('+2 month'));
        $lessonElementWebinar->setPriority(3);
        $elements->add($lessonElementWebinar);

        $lessonElementIframe = new LessonElement($lesson, new LessonElementType(LessonElementType::IFRAME));
        $lessonElementIframe->setIframe('<iframe width="560" height="315" src="https://www.youtube.com/embed/KYI_-heyM40" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>');
        $lessonElementIframe->setPriority(2);
        $elements->add($lessonElementIframe);

        $lessonElementText = new LessonElement($lesson, new LessonElementType(LessonElementType::TEXT));
        $lessonElementText->setText('<p>После того как вы добавили урок, внутри урока вы можете добавить следующие типы контента в любом порядке:</p><ul><li><h2 style="font-style:italic">текст с <u><em>форматированием</em></u> WYSIWIG с <span style="background-color:#1abc9c">возможностью</span> изменять <span style="font-family:Courier New,Courier,monospace">шрифты</span>, <span style="font-size:18px">размеры</span>, <span style="color:#1abc9c">стили&nbsp;</span>и даже&nbsp;<img alt="indecision" src="http://newhbadmin1.baxtep.com/bundles/fosckeditor/plugins/smiley/images/whatchutalkingabout_smile.png" style="height:23px; width:23px" title="indecision" />&nbsp;смайликами&nbsp;и другими возможностями полнофункционального текстового редактора</h2></li><li>Опросы</li><li>блоки Iframe - например презентация Youtube или Слайды Google</li><li>Файлы (любой формат видео или PDF)</li><li>Вебинар</li><li>Консультацию</li></ul>');
        $lessonElementText->setPriority(1);
        $elements->add($lessonElementText);


        $lessonElementQuiz = new LessonElement($lesson, new LessonElementType(LessonElementType::QUESTION));
        $lessonElementQuiz->setPriority(0);
        $question = new QuizQuestion();
        $question->setLessonElement($lessonElementQuiz);
        $question->setQuestionText('Вы знаете как перетаскивать блоки контента внутри урока?');

        $answer1 = new QuizQuestionAnswer();
        $answer1->setAnswerText('Да, необходимо потянуть за крестик');
        $answer1->setIsRight(false);
        $answer1->setQuestion($question);
        $question->addAnswer($answer1);

        $answer2 = new QuizQuestionAnswer();
        $answer2->setAnswerText('Нет, я не знаю');
        $answer2->setIsRight(true);
        $answer2->setQuestion($question);
        $question->addAnswer($answer2);
        $lessonElementQuiz->setQuestions(new ArrayCollection([$question]));

        $elements->add($lessonElementQuiz);

        $lesson->setElements($elements);
    }
}