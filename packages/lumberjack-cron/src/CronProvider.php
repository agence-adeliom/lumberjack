<?php

namespace Adeliom\Lumberjack\Cron;

use Rareloop\Lumberjack\Config;
use Rareloop\Lumberjack\Providers\ServiceProvider;

final class CronProvider extends ServiceProvider
{
    /**
     * Register all cronjob listed into the config file
     * @param Config $config
     */
    public function boot(Config $config): void
    {
        $cronsToRegister = $config->get('cron.register');
        if (is_array($cronsToRegister)) {
            foreach ($cronsToRegister as $cron) {
                $cron::register();
            }
        }
    }
}
