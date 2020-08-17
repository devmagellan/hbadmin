<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Entity\Types;


/**
 * This class used with Funnel Priority
 */
class FunnelType
{
    public const ORGANIC = 'ORGANIC';
    public const LEAD_COLLECTIONS = 'LEAD_COLLECTIONS';
    public const BROKEN_BASKET = 'BROKEN_BASKET';
    public const POST_SALE = 'POST_SALE';
    public const CROSS_SALE = 'CROSS_SALE';
    public const HOT_LEADS = 'HOT_LEADS';
    public const LAYER_CAKES = 'LAYER_CAKES';
    public const MINI_COURSE = 'MINI_COURSE';
    public const WEBINAR = 'WEBINAR';
    public const DOWN_SELLS = 'DOWN_SELLS';
    public const WALKER_START = 'WALKER_START';
    public const AUTHOR_CLUB = 'AUTHOR_CLUB';
    public const EDUCATIONAL = 'EDUCATIONAL';
    public const ONE_TIME_OFFER = 'ONE_TIME_OFFER';

    /**
     * @var string
     */
    private $status;

    public function __construct(string $status)
    {
        if (!in_array($status, $this->possibleTypes())) {
            throw new \InvalidArgumentException('Incorrect funnel type');
        }

        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        $names = $this->getFunnelNames();

        if (array_key_exists($this->getValue(), $names)) {
            return $names[$this->getValue()];
        }

        throw new \InvalidArgumentException('Incorrect funnel type. Cannot resolve funnel name.');
    }

    /**
     * @return array
     */
    public static function possibleTypes(): array
    {
        return [
            self::ORGANIC,
            self::LEAD_COLLECTIONS,
            self::BROKEN_BASKET,
            self::POST_SALE,
            self::CROSS_SALE,
            self::HOT_LEADS,
            self::LAYER_CAKES,
            self::MINI_COURSE,
            self::WEBINAR,
            self::DOWN_SELLS,
            self::WALKER_START,
            self::AUTHOR_CLUB,
            self::EDUCATIONAL,
            self::ONE_TIME_OFFER,
        ];
    }

    /**
     * @return array
     */
    private function getFunnelNames()
    {
        return [
            self::ORGANIC          => 'Органическая воронка',
            self::LEAD_COLLECTIONS => 'Вебинар',
            self::BROKEN_BASKET    => 'Брошенная корзина',
            self::POST_SALE        => 'Допродажа',
            self::CROSS_SALE       => 'Кросс продажа',
            self::HOT_LEADS        => 'Горячие лиды',
            self::LAYER_CAKES      => 'Слоеный пирог',
            self::MINI_COURSE      => 'Мини курс',
            self::WEBINAR          => 'Вебинар',
            self::DOWN_SELLS       => 'Выгодная формула',
            self::WALKER_START     => 'Запуск по волкеру',
            self::AUTHOR_CLUB      => 'Клуб авторов',
            self::EDUCATIONAL      => 'Образовательная',
            self::ONE_TIME_OFFER   => 'One Time Offer',
        ];
    }

    /**
     * @param string $key
     *
     * @return int
     */
    public static function getDefaultPriority(string $key)
    {

        $defaultPriorities = [
            self::ORGANIC          => 1,
            self::LEAD_COLLECTIONS => 2,
            self::BROKEN_BASKET    => 3,
            self::POST_SALE        => 4,
            self::CROSS_SALE       => 5,
            self::HOT_LEADS        => 6,
            self::LAYER_CAKES      => 7,
            self::MINI_COURSE      => 8,
            self::WEBINAR          => 9,
            self::DOWN_SELLS       => 10,
            self::WALKER_START     => 11,
            self::AUTHOR_CLUB      => 12,
            self::EDUCATIONAL      => 13,
            self::ONE_TIME_OFFER   => 14,
        ];

        if (array_key_exists($key, $defaultPriorities)) {
            return $defaultPriorities[$key];
        }

        throw new \InvalidArgumentException('Default priority not found for funnel key '.$key);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getValue();
    }
}
