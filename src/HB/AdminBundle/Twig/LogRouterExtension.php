<?php

declare(strict_types = 1);

namespace HB\AdminBundle\Twig;

use HB\AdminBundle\Entity\ActionLog;
use HB\AdminBundle\Entity\AdditionalBlock;
use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Entity\Lesson;
use HB\AdminBundle\Entity\LessonElement;
use HB\AdminBundle\Entity\LessonSection;
use HB\AdminBundle\Entity\QuizQuestion;
use HB\AdminBundle\Entity\QuizQuestionAnswer;
use HB\AdminBundle\Entity\SaleFunnel\CrossSale\DiscountCourse;
use HB\AdminBundle\Entity\SaleFunnel\MiniCourse\MiniLesson;
use HB\AdminBundle\Entity\SaleFunnel\Organic\CompanyLogo;
use HB\AdminBundle\Entity\SaleFunnel\Organic\FeedBackVideo;
use HB\AdminBundle\Entity\SaleFunnel\Organic\KnowledgeSkills;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelAuthorClub;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelBrokenBasket;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelCrossSale;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelDownSell;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelEducational;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelHotLeads;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelLayerCake;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelLeadCollection;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelMiniCourse;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelOneTimeOffer;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelOrganic;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelPartner;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelPostSale;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelWebinar;
use HB\AdminBundle\Entity\SaleFunnel\Webinar\WebinarBonus;
use HB\AdminBundle\Entity\SaleFunnel\Webinar\WebinarOffer;
use HB\AdminBundle\Entity\SaleFunnel\Webinar\WebinarThesis;
use HB\AdminBundle\Helper\ClassNameHelper;
use Symfony\Component\Routing\RouterInterface;
use Twig\Extension\AbstractExtension;

class LogRouterExtension extends AbstractExtension
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var array
     */
    private $preparedRoutes = [];

    /**
     * LogRouterExtension constructor.
     *
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
        $this->prepareRoutes();
    }

    /**
     * @return array|\Twig_Filter[]
     */
    public function getFilters()
    {
        return [
            new \Twig_Filter('log_route', [$this, 'getLogRoute']),
        ];
    }

    /**
     * @param ActionLog $log
     *
     * @return string
     */
    public function getLogRoute(ActionLog $log)
    {
        $url = '#';
        $route = $this->findLogRoute($log);

        if ($route) {
            $url = $route['course_id']
                ? $this->router->generate($route['route'], ['id' => $log->getCourse()->getId()])
                : $this->router->generate($route['route'], ['id' => $log->getObjectId()]);
        }

        return $url;
    }

    /**
     * Try to find route for customer log
     *
     * @param ActionLog $actionLog
     *
     * @return |null
     */
    private function findLogRoute(ActionLog $actionLog)
    {
        $routeForLog = null;

        foreach ($this->preparedRoutes as $key => $route) {
            if ($route['class'] === $actionLog->getObject()) {
                $routeForLog = $this->preparedRoutes[$key];
            }
        }

        return $routeForLog;
    }

    /**
     * Manually chosen routes for customer action log
     *
     * @return array
     */
    private function getAvailableRoutes()
    {

        $routes = [
            ['class'     => SalesFunnelWebinar::class,
             'route'     => 'hb.sale_funnel.webinar.edit',
             'course_id' => false,
            ],
            ['class'     => LessonSection::class,
             'route'     => 'hb.lesson_section.list',
             'course_id' => true,
            ],
            ['class'     => Lesson::class,
             'route'     => 'hb.lesson.edit',
             'course_id' => false,
            ],
            ['class'     => Course::class,
             'route'     => 'hb.course.edit',
             'course_id' => true,
            ],
            ['class'     => SalesFunnelLayerCake::class,
             'route'     => 'hb.sale_funnel.layer_cake.list',
             'course_id' => true,
            ],
            ['class'     => SalesFunnelHotLeads::class,
             'route'     => 'hb.sale_funnel.hot_leads.edit',
             'course_id' => false,
            ],
            ['class'     => SalesFunnelMiniCourse::class,
             'route'     => 'hb.sale_funnel.mini_course.edit',
             'course_id' => true,
            ],
            ['class'     => SalesFunnelBrokenBasket::class,
             'route'     => 'hb.course.edit',
             'course_id' => true,
            ],
            ['class'     => SalesFunnelPostSale::class,
             'route'     => 'hb.course.edit',
             'course_id' => true,
            ],
            ['class'     => SalesFunnelAuthorClub::class,
             'route'     => 'hb.course.edit',
             'course_id' => true,
            ],
            ['class'     => SalesFunnelPartner::class,
             'route'     => 'hb.sale_funnel.partner.list',
             'course_id' => true,
            ],
            ['class'     => SalesFunnelEducational::class,
             'route'     => 'hb.sale_funnel.educational.edit',
             'course_id' => false,
            ],
            ['class'     => SalesFunnelOrganic::class,
             'route'     => 'hb.sale_funnel.organic.edit',
             'course_id' => true,
            ],
            ['class'     => SalesFunnelLeadCollection::class,
             'route'     => 'hb.sale_funnel.lead_collection.edit.lead_magnet',
             'course_id' => false,
            ],
            ['class'     => KnowledgeSkills::class,
             'route'     => 'hb.sale_funnel.organic.edit',
             'course_id' => true,
            ],
            ['class'     => LessonElement::class,
             'route'     => 'hb.lesson_section.list',
             'course_id' => true,
            ],
            ['class'     => QuizQuestionAnswer::class,
             'route'     => 'hb.lesson_section.list',
             'course_id' => true,
            ],
            ['class'     => QuizQuestion::class,
             'route'     => 'hb.lesson_section.list',
             'course_id' => true,
            ],
            ['class'     => SalesFunnelCrossSale::class,
             'route'     => 'hb.sale_funnel.cross_sale.edit',
             'course_id' => true,
            ],
            ['class'     => MiniLesson::class,
             'route'     => 'hb.sale_funnel.mini_course.edit',
             'course_id' => true,
            ],
            ['class'     => WebinarThesis::class,
             'route'     => 'hb.sale_funnel.webinar.list',
             'course_id' => true,
            ],
            ['class'     => WebinarBonus::class,
             'route'     => 'hb.sale_funnel.webinar.list',
             'course_id' => true,
            ],
            ['class'     => WebinarOffer::class,
             'route'     => 'hb.sale_funnel.webinar.list',
             'course_id' => true,
            ],
            ['class'     => SalesFunnelDownSell::class,
             'route'     => 'hb.sale_funnel.down_sell.edit',
             'course_id' => false,
            ],
            ['class'     => SalesFunnelOneTimeOffer::class,
             'route'     => 'hb.sale_funnel.one_time_offer.edit',
             'course_id' => false,
            ],
            ['class'     => FeedBackVideo::class,
             'route'     => 'hb.sale_funnel.organic.edit',
             'course_id' => true,
            ],
            ['class'     => AdditionalBlock::class,
             'route'     => 'hb.sale_funnel.organic.edit',
             'course_id' => true,
            ],
            ['class'     => CompanyLogo::class,
             'route'     => 'hb.sale_funnel.organic.edit',
             'course_id' => true,
            ],
            ['class'     => DiscountCourse::class,
             'route'     => 'hb.sale_funnel.cross_sale.edit',
             'course_id' => true,
            ],

        ];

//        $route = $this->router->generate('hb.sale_funnel.one_time_offer.edit');
/*
| SuccessHistory                     | ----
| SaleFunnelOrganicPriceBlockThesis  | can try later with refactor route to course id (+funnel id)
| SaleFunnelOrganicPriceBlockSetting | can try later with refactor route to course id (+funnel id)
| WebinarBonus                       | can try later with refactor route to course id (+funnel id)
| WebinarOffer                       | can try later with refactor route to course id (+funnel id)
| Letter                             | ??
 */
        return $routes;
    }

    /**
     * Replace class without namespace
     */
    private function prepareRoutes()
    {
        $prepared = $this->getAvailableRoutes();

        foreach ($prepared as $key => $classRoute) {
            $prepared[$key]['class'] = ClassNameHelper::getClassName($classRoute['class']);
        }

        $this->preparedRoutes = $prepared;
    }

}
