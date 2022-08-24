<?php

namespace Adeliom\Lumberjack\Webpack;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @package App\TwigExtensions
 */
class WebpackEncoreExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('encore_entry_js_files', [WebpackEncore::class, "jsFiles"]),
            new TwigFunction('encore_entry_css_files', [WebpackEncore::class, "cssFiles"]),
            new TwigFunction('encore_entry_script_tags', [WebpackEncore::class, "scriptTags"]),
            new TwigFunction('encore_entry_link_tags', [WebpackEncore::class, "linkTags"]),
            new TwigFunction('asset', [WebpackEncore::class, "asset"]),
        ];
    }
}
