<?php

namespace Adeliom\Lumberjack\Assets;

use Adeliom\Lumberjack\Assets\Entrypoint\EntrypointLookup;
use Adeliom\Lumberjack\Assets\Provider\ViteEntrypointLookup;
use Adeliom\Lumberjack\Assets\Provider\WebpackEntrypointLookup;
use Adeliom\Lumberjack\Assets\Tag\TagRenderer;
use Adeliom\Lumberjack\Assets\Twig\EntryFilesTwigExtension;
use Adeliom\Lumberjack\Assets\Wordpress\Enqueuer;
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
        $directory = ConfigFacade::get("assets.build_directory", 'build');
        $strictMode = ConfigFacade::get("assets.strict_mode", false);

        $this->app->bind("assets.provider.webpack", new WebpackEntrypointLookup($directory, $strictMode));
        $this->app->bind("assets.provider.vite", new ViteEntrypointLookup($directory, $strictMode));
        $this->app->bind("assets.tag_renderer", new TagRenderer());

        $assetManager = new AssetManager();
        $this->app->bind(Assets::accessor(), $assetManager);
        $this->app->bind(Assets::class, $assetManager);
    }

    public function boot(Config $config): void
    {
        add_filter('timber/twig', function ($twig) {
            $twig->addExtension(new EntryFilesTwigExtension());
            return $twig;
        });

        add_filter('script_loader_tag',function($tag, $handle, $src){
            return Enqueuer::scriptAndStyleTagAttributeAdder($tag, $handle, $src, null, false);
        },10,4);
        add_filter('style_loader_tag',function($tag, $handle, $src, $media){
            return Enqueuer::scriptAndStyleTagAttributeAdder($tag, $handle, $src, $media, true);
        },10,4);
    }
}
