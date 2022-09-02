<?php

namespace Adeliom\Lumberjack\Assets;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AssetsExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('assets_entry_js_files', [Assets::class, "jsFiles"]),
            new TwigFunction('assets_entry_css_files', [Assets::class, "cssFiles"]),
            new TwigFunction('assets_entry_script_tags', [Assets::class, "scriptTags"]),
            new TwigFunction('assets_entry_link_tags', [Assets::class, "linkTags"]),
            new TwigFunction('asset', [Assets::class, "asset"]),
        ];
    }
}
