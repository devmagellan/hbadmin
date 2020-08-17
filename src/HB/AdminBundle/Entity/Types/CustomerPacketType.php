<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Entity\Types;

/**
 * This class used with Customer as "constraint"
 */
class CustomerPacketType
{
    public const PROFESSIONAL = 'PROFESSIONAL';
    public const PREMIUM = 'PREMIUM';
    public const VIP = 'VIP';
    public const CUSTOM = 'CUSTOM';
    public const WEBINAR = 'WEBINAR';
    public const ONLINE_SCHOOL = 'ONLINE_SCHOOL';
    public const BASIC = 'BASIC';
    public const EXTENDED = 'EXTENDED';

    /**
     * @var string
     */
    private $type;

    public function __construct(string $type)
    {
        if (!in_array($type, self::possibleTypes())) {
            throw new \InvalidArgumentException('Неверный тип пакета пользователя.');
        }

        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public static function possibleTypes(): array
    {
        return [
            self::PREMIUM,
            self::PROFESSIONAL,
            self::CUSTOM,
            self::VIP,
            self::WEBINAR,
            self::ONLINE_SCHOOL,
            self::BASIC,
            self::EXTENDED,
        ];
    }

    /**
     * @param string $type
     *
     * @return string
     */
    public static function getName(string $type)
    {
        $name = 'UNDEFINED PACKET';
        if ($type === CustomerPacketType::CUSTOM) {
            $name = 'Индивидуальный';
        } else if ($type === CustomerPacketType::VIP) {
            $name = 'VIP';
        } else if ($type === CustomerPacketType::PROFESSIONAL) {
            $name = 'Профессиональный';
        } else if ($type === CustomerPacketType::PREMIUM) {
            $name = 'Премиум';
        } else if ($type === CustomerPacketType::WEBINAR) {
            $name = 'Вебинарный';
        } else if ($type === CustomerPacketType::ONLINE_SCHOOL) {
            $name = 'Онлайн-школа';
        } else if ($type === CustomerPacketType::BASIC) {
            $name = 'Базовый';
        } else if ($type === CustomerPacketType::EXTENDED) {
            $name = 'Расширенный';
        }

        return $name;
    }
}
