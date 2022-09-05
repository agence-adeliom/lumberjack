<?php

namespace Adeliom\Lumberjack\Assets;

use Adeliom\Lumberjack\Assets\Entrypoint\EntrypointLookup;
use Adeliom\Lumberjack\Assets\Provider\ViteEntrypointLookup;
use Adeliom\Lumberjack\Assets\Provider\WebpackEntrypointLookup;
use Adeliom\Lumberjack\Assets\Tag\TagRenderer;
use Adeliom\Lumberjack\Assets\Twig\EntryFilesTwigExtension;
use Adeliom\Lumberjack\Assets\Wordpress\Enqueuer;
use Adeliom\Lumberjack\Assets\Wordpress\PreLoadAssets;
use Rareloop\Lumberjack\Facades\Config as ConfigFacade;
use Rareloop\Lumberjack\Helpers;
use Rareloop\Lumberjack\Providers\ServiceProvider;
use Symfony\Component\WebLink\HttpHeaderSerializer;

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
        $this->app->bind("assets.preloader", new PreLoadAssets());

        $assetManager = new AssetManager();
        $this->app->bind(Assets::accessor(), $assetManager);
        $this->app->bind(Assets::class, $assetManager);
    }

    public function boot(): void
    {
        add_filter('timber/twig', function ($twig) {
            $twig->addExtension(new EntryFilesTwigExtension());
            return $twig;
        });

        add_filter('script_loader_tag', function ($tag, $handle, $src) {
            return Enqueuer::scriptAndStyleTagAttributeAdder($tag, $handle, $src, null, false);
        }, 10, 4);
        add_filter('style_loader_tag', function ($tag, $handle, $src, $media) {
            return Enqueuer::scriptAndStyleTagAttributeAdder($tag, $handle, $src, $media, true);
        }, 10, 4);

        if (ConfigFacade::get("assets.preload", false)) {
            add_action('send_headers', function () {
                $preloader = Helpers::app()->get("assets.preloader");
                $preloader->generateWebLink();
                $links = $preloader->getLinks();
                if (!empty($links)) {
                    header('Link: ' . (new HttpHeaderSerializer())->serialize($links));
                }
            }, 99);
        }
    }
}
