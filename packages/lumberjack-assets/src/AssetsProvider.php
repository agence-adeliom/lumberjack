<?php

namespace Adeliom\Lumberjack\Assets;

use Rareloop\Lumberjack\Config;
use Rareloop\Lumberjack\Facades\Config as ConfigFacade;
use Rareloop\Lumberjack\Providers\ServiceProvider;

class AssetsProvider extends ServiceProvider
{
    /**
     * Register any app specific items into the container
     */
    public function register()
    {
        $directory = ConfigFacade::get("assets.directory");
        $assetManager = new AssetManager(sprintf('%s/%s', get_template_directory(), $directory));
        $this->app->bind(Assets::accessor(), $assetManager);
        $this->app->bind(Assets::class, $assetManager);
    }

    public function boot(Config $config): void
    {
        add_filter('timber/twig', function ($twig) {
            $twig->addExtension(new AssetsExtension());
            return $twig;
        });
    }
}
