<?php

namespace Adeliom\Lumberjack\Taxonomy;

use Rareloop\Lumberjack\Config;
use Rareloop\Lumberjack\Providers\ServiceProvider;

class CustomTaxonomyServiceProvider extends ServiceProvider
{
    public function boot(Config $config)
    {
        $taxonomiesToRegister = $config->get('taxonomies.register');

        foreach ($taxonomiesToRegister as $taxonomy) {
            $taxonomy::register();
        }
    }
}
