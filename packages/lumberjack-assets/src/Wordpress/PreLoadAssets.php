<?php

namespace Adeliom\Lumberjack\Assets\Wordpress;

use Adeliom\Lumberjack\Assets\Tag\TagRenderer;
use Rareloop\Lumberjack\Helpers;
use Symfony\Component\WebLink\GenericLinkProvider;
use Symfony\Component\WebLink\Link;

class PreLoadAssets
{
    private GenericLinkProvider $linkProvider;
    private TagRenderer $tagRenderer;

    public function __construct()
    {
        $this->linkProvider = new GenericLinkProvider();
        $this->tagRenderer = Helpers::app()->get("assets.tag_renderer");
    }

    public function generateWebLink()
    {
        $defaultAttributes = $this->tagRenderer->getDefaultAttributes();
        $crossOrigin = $defaultAttributes['crossorigin'] ?? false;

        foreach ($this->tagRenderer->getRenderedScripts() as $href) {
            $link = $this->createLink('preload', $href)->withAttribute('as', 'script');

            if (false !== $crossOrigin) {
                $link = $link->withAttribute('crossorigin', $crossOrigin);
            }

            $this->linkProvider = $this->linkProvider->withLink($link);
        }

        foreach ($this->tagRenderer->getRenderedStyles() as $href) {
            $link = $this->createLink('preload', $href)->withAttribute('as', 'style');

            if (false !== $crossOrigin) {
                $link = $link->withAttribute('crossorigin', $crossOrigin);
            }

            $this->linkProvider = $this->linkProvider->withLink($link);
        }
    }

    public function getLinks(): array
    {
        return $this->linkProvider->getLinks();
    }

    private function createLink(string $rel, string $href): Link
    {
        return new Link($rel, $href);
    }
}
