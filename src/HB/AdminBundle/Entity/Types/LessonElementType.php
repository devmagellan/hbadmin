<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Entity\Types;

/**
 * This class used with LessonElement as "constraint"
 */

class LessonElementType
{
    public const FILE = 'FILE';
    public const TEXT = 'TEXT';
    public const QUESTION = 'QUESTION';
    public const IFRAME = 'IFRAME';
    public const WEBINAR = 'WEBINAR';
    public const CONSULTATION = 'CONSULTATION';

    /**
     * @var string
     */
    private $status;

    public function __construct(string $status)
    {
        if (!in_array($status, $this->possibleTypes())) {
            throw new \InvalidArgumentException('Неверный тип элемента урока.');
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
        $name = '';
        if (self::TEXT === $this->status) {
            $name = 'Текст';
        } elseif (self::IFRAME === $this->status) {
            $name = 'IFrame';
        } elseif (self::FILE === $this->status) {
            $name = 'Файл';
        } elseif (self::QUESTION === $this->status) {
            $name = 'Опрос';
        } elseif (self::WEBINAR === $this->status) {
            $name = 'Вебинар';
        } elseif (self::CONSULTATION === $this->status) {
            $name = 'Консультация';
        }

        return $name;
    }

    /**
     * @return string
     */
    public function getIconName(): string
    {
        $name = '';
        if (self::TEXT === $this->status) {
            $name = 'fa-file-text-o';
        } else if (self::IFRAME === $this->status) {
            $name = 'fa-html5';
        } else if (self::FILE === $this->status) {
            $name = 'fa-file';
        } else if (self::QUESTION === $this->status) {
            $name = 'fa-question';
        } else if (self::WEBINAR=== $this->status) {
            $name = 'fa-rss';
        } else if (self::CONSULTATION === $this->status) {
            $name = 'fa-users';
        }

        return $name;
    }


        /**
     * @return array
     */
    private function possibleTypes(): array
    {
        return [
            self::FILE,
            self::TEXT,
            self::QUESTION,
            self::IFRAME,
            self::WEBINAR,
            self::CONSULTATION,
        ];
    }
}
