<?php

namespace Adeliom\Lumberjack\Assets\Twig;

use Adeliom\Lumberjack\Assets\Entrypoint\EntrypointLookupInterface;
use Adeliom\Lumberjack\Assets\Tag\TagRenderer;
use Rareloop\Lumberjack\Facades\Config;
use Rareloop\Lumberjack\Helpers;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class EntryFilesTwigExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('entry_js_files', [$this, 'getJsFiles']),
            new TwigFunction('entry_css_files', [$this, 'getCssFiles']),
            new TwigFunction('entry_script_tags', [$this, 'renderScriptTags'], ['is_safe' => ['html']]),
            new TwigFunction('entry_link_tags', [$this, 'renderLinkTags'], ['is_safe' => ['html']]),
            new TwigFunction('entry_exists', [$this, 'entryExists']),
            new TwigFunction('asset', [$this, 'getAsset']),
        ];
    }

    public function getJsFiles(string $entryName): array
    {
        return $this->getEntrypointLookup()->getJavaScriptFiles($entryName);
    }

    public function getCssFiles(string $entryName): array
    {
        return $this->getEntrypointLookup()->getCssFiles($entryName);
    }

    public function renderScriptTags(string $entryName, string $packageName = null, array $attributes = []): string
    {
        return $this->getTagRenderer()->renderScriptTags($entryName, $packageName, $attributes);
    }

    public function renderLinkTags(string $entryName, string $packageName = null, array $attributes = []): string
    {
        return $this->getTagRenderer()->renderLinkTags($entryName, $packageName, $attributes);
    }

    public function entryExists(string $entryName): bool
    {
        return $this->getEntrypointLookup()->entryExists($entryName);
    }

    public function getAsset(string $ressource): ?string
    {
        return $this->getEntrypointLookup()->getAsset($ressource);
    }

    private function getEntrypointLookup(): EntrypointLookupInterface
    {
        return Helpers::app()->get(sprintf('assets.provider.%s', Config::get("assets.provider", "webpack")));
    }

    private function getTagRenderer(): TagRenderer
    {
        return Helpers::app()->get('assets.tag_renderer');
    }
}
