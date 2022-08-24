<?php

namespace Adeliom\Lumberjack\Event;

use ReflectionClass;
use ReflectionException;

/**
 * @package Adeliom\Lumberjack\Event
 */
abstract class Event
{
    /**
     * The event name
     * @return string
     */
    abstract public static function getEvent(): string;

    /**
     * The event priority (default: 10)
     * @return int
     */
    public static function getPriority(): int
    {
        return 10;
    }

    /**
     * The event function
     * @return void
     */
    abstract public static function handle($args = []): void;
}
