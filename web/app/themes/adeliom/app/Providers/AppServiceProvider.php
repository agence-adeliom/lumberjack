<?php

declare(strict_types=1);

namespace App\Providers;

use Rareloop\Lumberjack\Config;
use Rareloop\Lumberjack\Providers\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Perform any additional boot required for this application
     */
    public function boot(Config $config): void
    {
        $stages = $config->get('stages');
        if (is_array($stages)) {
            define('ENVIRONMENTS', serialize($stages));
        }

        $mimesTypes = $config->get('images.mimes_types', []);
        add_filter("upload_mimes", static function (array $mime_types = []) use ($mimesTypes) {
            return array_merge($mime_types, $mimesTypes);
        });
    }
}
