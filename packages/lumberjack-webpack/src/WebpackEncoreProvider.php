<?php

namespace Adeliom\Lumberjack\Webpack;

use PhpCsFixer\Config;
use Rareloop\Lumberjack\Providers\ServiceProvider;

class WebpackEncoreProvider extends ServiceProvider
{
    /**
     * Register any app specific items into the container
     */
    public function register()
    {
        $directory = config("webpack.directory");
        $webpackEncore = new AssetManager(sprintf('%s/%s', get_template_directory(), $directory));
        $this->app->bind(WebpackEncore::accessor(), $webpackEncore);
        $this->app->bind(WebpackEncore::class, $webpackEncore);
    }

    public function boot(Config $config): void
    {
        add_filter('timber/twig', function ($twig) use ($config) {
            $twig->addExtension(new WebpackEncoreExtension());
            return $twig;
        });
    }
}
