<?php

namespace Adeliom\Lumberjack\Cron;

use ReflectionClass;

/**
 * @package Adeliom\Lumberjack\Cron
 */
abstract class Cron
{
    /**
     * Setup de interval between executions
     *
     * @var int[]
     */
    protected $every = [
        'seconds' => 0,
        'minutes' => 0,
        'hours' => 0,
        'days' => 0,
        'weeks' => 0,
        'months' => 0,
    ];

    /**
     * Cron task handler
     * @return void
     */
    abstract public static function handle(): void;

    /**
     * Register the cron task into Wordpress's scheduler
     */
    public static function register()
    {
        $class = static::class;
        $self = new $class();
        $slug = $self->slug();

        add_filter('cron_schedules', fn ($schedules) => $self->scheduleFilter($schedules));

        if (!wp_next_scheduled($slug)) {
            wp_schedule_event(time(), $self->schedule(), $slug);
        }

        if (method_exists($self, 'handle')) {
            add_action($slug, [$self, 'handle']);
        }
    }

    /**
     * Function executed by the `cron_schedules` filter
     * @see https://developer.wordpress.org/reference/hooks/cron_schedules/
     *
     * @param $schedules
     * @return mixed
     */
    public function scheduleFilter($schedules)
    {
        $interval = $this->calculateInterval();

        if (!array_key_exists($this->schedule(), $schedules)) {
            $schedules[$this->schedule()] = [
                'interval' => $interval,
                'display' => 'Every ' . floor($interval / 60) . ' minutes',
            ];
        }

        return $schedules;
    }

    /**
     * Calculate the interval for Wordpress compatibility
     *
     * @return int
     */
    public function calculateInterval(): int
    {
        if (!is_array($this->every)) {
            throw new \RuntimeException("Interval must be an array");
        }

        if (count(array_filter(array_keys($this->every), 'is_string')) <= 0) {
            throw new \RuntimeException("Cron::\$interval must be an assoc array");
        }

        $interval = 0;
        $multipliers = [
            'seconds' => 1,
            'minutes' => 60,
            'hours' => 3600,
            'days' => 86400,
            'weeks' => 604800,
            'months' => 2628000,
        ];

        foreach ($multipliers as $unit => $multiplier) {
            if (isset($this->every[$unit]) && is_int($this->every[$unit])) {
                $interval += $this->every[$unit] * $multiplier;
            }
        }

        return $interval;
    }

    /**
     * Return the schedule key
     */
    public function schedule(): string
    {
        return 'schedule_' . $this->slug();
    }

    /**
     * Generate the job slug
     */
    public function slug(): string
    {
        $reflect = new ReflectionClass($this);
        $class = $reflect->getShortName();
        return 'wp_cron__' . strtolower($class);
    }
}
