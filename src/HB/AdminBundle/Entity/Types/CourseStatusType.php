<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Entity\Types;

/**
 * This class used with Course as "constraint"
 */

class CourseStatusType
{
    public const STATUS_IN_PROGRESS = 'IN_PROGRESS';
    public const STATUS_WAIT_PUBLISH = 'WAIT_PUBLISH';
    public const STATUS_PUBLISHED = 'PUBLISHED';

    public const SANDBOX = 'SANDBOX'; // it is not status, but for filters need

    /**
     * @var string
     */
    private $status;

    public function __construct(string $status)
    {
        if (!in_array($status, $this->possibleTypes())) {
            throw new \InvalidArgumentException('Неверный статус курса. Статус - ' .$status);
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
        $name = 'Неопределенный статус';

        if (self::STATUS_IN_PROGRESS === $this->status) {
            $name = 'Наполнение';
        } elseif (self::STATUS_WAIT_PUBLISH === $this->status) {
            $name = 'Публикуется';
        } elseif (self::STATUS_PUBLISHED === $this->status) {
            $name = 'Опубликован';
        }

        return $name;
    }

    /**
     * @return array
     */
    private function possibleTypes(): array
    {
        return [
            self::STATUS_IN_PROGRESS,
            self::STATUS_WAIT_PUBLISH,
            self::STATUS_PUBLISHED,
        ];
    }
}
