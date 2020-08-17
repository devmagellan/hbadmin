<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Service;


use HB\AdminBundle\Component\Mailer\Mailer;
use HB\AdminBundle\Component\Mailer\Model\Receiver;
use HB\AdminBundle\Component\Mailer\Model\Sender;
use HB\AdminBundle\Component\Model\Email;
use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelDownSell;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelWebinar;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class EmailSender
{
    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var string
     */
    private $emailFrom;

    /**
     * EmailSender constructor.
     *
     * @param Mailer          $mailer
     * @param RouterInterface $router
     * @param string          $emailFrom
     */
    public function __construct(Mailer $mailer, RouterInterface $router, string $emailFrom)
    {
        $this->mailer = $mailer;
        $this->router = $router;
        $this->emailFrom = $emailFrom;
    }

    /**
     * @param Course $course
     */
    public function coursePublishedEmail(Course $course)
    {
        $customer = $course->getOwner();

        $emailTemplate = '@HBAdmin/Mailer/Customer/course_published.html.twig';
        $link = $this->router->generate('login', [], UrlGeneratorInterface::ABSOLUTE_URL);

        $this->mailer->send(
            new Sender(new Email($this->emailFrom), 'Heartbeat Education | Образовательная Платформа'),
            new Receiver(new Email($customer->getEmail())),
            $emailTemplate,
            $customer->getFirstName().', поздравляем! Ваш продукт опубликован',
            [
                'customer'      => $customer,
                'course'        => $course,
                'link'          => $link,
                'externalLinks' => $this->getCourseExternalLinks($course),
            ]
        );
    }

    /**
     * @param Course $course
     *
     * @return array
     */
    private function getCourseExternalLinks(Course $course)
    {
        $links = [];

        $this->addLink($links, 'Онлайн Школа', $course->getLinkOnlineSchool());
        $this->addLink($links, 'Страница Оплаты', $course->getLinkPaymentPage());

        if ($course->getSalesFunnelOrganic() && $course->getSalesFunnelOrganic()->getExternalLink()) {
            $this->addLink($links, 'Органическая Целевая Страница', $course->getSalesFunnelOrganic()->getExternalLink());
        }

        if ($course->getSalesFunnelDownSells()->count() > 0) {
            /** @var SalesFunnelDownSell $downSell */
            foreach ($course->getSalesFunnelDownSells() as $downSell) {
                if ($downSell->getExternalLink()) {
                    $title = sprintf('Целевая Страница Выгодной Формулы #%s', $downSell->getId());
                    $this->addLink($links, $title, $downSell->getExternalLink());
                }
            }
        }

        if ($course->getSalesFunnelDownSells()->count() > 0) {
            /** @var SalesFunnelDownSell $downSell */
            foreach ($course->getSalesFunnelDownSells() as $downSell) {
                if ($downSell->getExternalLink()) {
                    $title = sprintf('Целевая Страница Выгодной Формулы #%s', $downSell->getId());
                    $this->addLink($links, $title, $downSell->getExternalLink());
                }
            }
        }

        if ($course->getSalesFunnelWebinar()->count() > 0) {
            /** @var SalesFunnelWebinar $webinar */
            foreach ($course->getSalesFunnelWebinar() as $webinar) {
                if ($webinar->getExternalLink()) {
                    $title = sprintf('Страница Регистрации на Вебинар #%s', $webinar->getId());
                    $this->addLink($links, $title, $webinar->getExternalLink());
                }
            }
        }

        if ($course->getSalesFunnelEducational() && $course->getSalesFunnelEducational()->getExternalLink()) {
            $this->addLink($links, 'Ваши Статьи в Блоге', $course->getSalesFunnelEducational()->getExternalLink());
        }

        return $links;
    }

    /**
     * @param array  $links
     * @param string $title
     * @param string $url
     */
    private function addLink(array &$links, string $title, string $url)
    {
        $links[] = [
            'title' => $title,
            'url' => $url,
        ];
    }
}