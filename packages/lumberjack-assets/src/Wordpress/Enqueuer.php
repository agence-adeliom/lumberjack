<?php

namespace Adeliom\Lumberjack\Assets\Wordpress;

use Adeliom\Lumberjack\Assets\Entrypoint\EntrypointLookupInterface;
use Adeliom\Lumberjack\Assets\Entrypoint\IntegrityDataProviderInterface;
use Rareloop\Lumberjack\Facades\Config;
use Rareloop\Lumberjack\Helpers;

class Enqueuer
{
    public const ATTRIB_PREFIX = 'att_&#';
    public const ATTRIB_PATTERN = '/' . self::ATTRIB_PREFIX . '(.+)/';

    public static function enqueue(string $name, string $entryName, array $config = []): array
    {
        $config = self::normalizeAssetConfig($config);
        $assets = self::register($name, $entryName, $config);

        $jses = $assets['js'];
        $csses = $assets['css'];
        foreach ($jses as $js) {
            \wp_enqueue_script($js['handle']);
        }

        foreach ($csses as $css) {
            \wp_enqueue_style($css['handle']);
        }

        return $assets;
    }

    private static function getEntrypointLookup(): EntrypointLookupInterface
    {
        return Helpers::app()->get(sprintf('assets.provider.%s', Config::get("assets.provider", "webpack")));
    }

    private static function getAssets(string $name, string $entryName): array
    {
        if (!self::getEntrypointLookup()->entryExists($entryName)) {
            throw new \InvalidArgumentException(
                \sprintf('Invalid entrypoint "%s"', $entryName)
            );
        }
        $entryPointLookup = self::getEntrypointLookup();
        $jsFiles = $entryPointLookup->getJavaScriptFiles($entryName);
        $cssFiles = $entryPointLookup->getCssFiles($entryName);

        $integrityHashes = ($entryPointLookup instanceof IntegrityDataProviderInterface) ? $entryPointLookup->getIntegrityData() : [];

        $js = [];
        $css = [];
        $defaultAttributes = [];
        if (false !== Config::get("assets.crossorigin", false)) {
            $defaultAttributes['crossorigin'] = Config::get("assets.crossorigin", false);
        }

        foreach ($jsFiles as $url) {
            $attributes = array_merge($defaultAttributes, Config::get("assets.script_attributes", []));
            if (isset($integrityHashes[$url])) {
                $attributes['integrity'] = $integrityHashes[$url];
            }
            $js[] = [
                'handle' => self::getHandle($name, $url, 'script'),
                'url' => self::getUrl($url),
                'attributes' => $attributes
            ];
        }

        foreach ($cssFiles as $url) {
            $attributes = array_merge($defaultAttributes, Config::get("assets.link_attributes", []));
            if (isset($integrityHashes[$url])) {
                $attributes['integrity'] = $integrityHashes[$url];
            }
            $css[] = [
                'handle' => self::getHandle($name, $url, 'style'),
                'url' => self::getUrl($url),
                'attributes' => $attributes
            ];
        }

        return [
            'css' => $css,
            'js' => $js,
        ];
    }

    private static function register(string $name, string $entryName, array $config): array
    {
        $config = self::normalizeAssetConfig($config);
        $assets = self::getAssets($name, $entryName);

        $jses = $assets['js'];
        $csses = $assets['css'];

        $deps = [
            'js' => [],
            'css' => []
        ];

        foreach ($jses as $js) {
            \wp_register_script(
                $js['handle'],
                $js['url'],
                array_merge($config['deps']['js'], $deps['js']),
                $config['version'],
                $config['in_footer']
            );
            $deps['js'][] = $js['handle'];
            foreach ($js['attributes'] ?? [] as $attr => $value) {
                \wp_script_add_data($js['handle'], self::ATTRIB_PREFIX . $attr, $value);
            }
        }

        foreach ($csses as $css) {
            \wp_register_style(
                $css['handle'],
                $css['url'],
                array_merge($config['deps']['css'], $deps['css']),
                $config['version'],
                $config['media']
            );
            $deps['css'][] = $css['handle'];
            foreach ($css['attributes'] ?? [] as $attr => $value) {
                \wp_style_add_data($css['handle'], self::ATTRIB_PREFIX . $attr, $value);
            }
        }
        return $assets;
    }

    private static function normalizeAssetConfig(array $config): array
    {
        return wp_parse_args(
            $config,
            [
                'version' => null,
                'deps' => [
                    'js' => [],
                    'css' => []
                ],
                'in_footer' => true,
                'media' => 'all',
                'attributes' => []
            ]
        );
    }

    private static function getUrl(string $asset): string
    {
        $themeUrl = get_theme_file_uri();
        $asset = str_replace(parse_url($themeUrl, PHP_URL_PATH), "", $asset);
        return get_theme_file_uri($asset);
    }

    private static function getHandle(string $name, string $path, string $type = 'script'): string
    {
        if (!\in_array($type, ['script', 'style'], true)) {
            throw new \InvalidArgumentException('Type has to be either script or style.');
        }
        return 'assets_'
            . sanitize_title($path)
            . '_'
            . $type;
    }

    /**
     * Callback for WP to hit before echoing out an enqueued resource. This callback specifically checks for any key-value pairs that have been added through `add_data()` and are prefixed with a special value to indicate they should be injected into the final HTML
     * @param string $tag - Will be the full string of the tag (`<link>` or `<script>`)
     * @param string $handle - The handle that was specified for the resource when enqueuing it
     * @param string $src - the URI of the resource
     * @param string|null $media - if resources is style, should be the target media, else null
     * @param bool $isStyle - If the resource is a stylesheet
     */
    public static function scriptAndStyleTagAttributeAdder($tag, $handle, $src, $media, $isStyle)
    {
        $extraAttrs = array();
        $nodeName = '';

        // Get the WP_Dependency instance for this handle, and grab any extra fields
        if ($isStyle) {
            $nodeName = 'link';
            $extraAttrs = wp_styles()->registered[$handle]->extra;
        } else {
            $nodeName = 'script';
            $extraAttrs = wp_scripts()->registered[$handle]->extra;
        }

        // Check stored properties on WP resource instance against our pattern
        $attribsToAdd = array();
        foreach ($extraAttrs as $fullAttrKey => $attrVal) {
            $matches = array();
            preg_match(self::ATTRIB_PATTERN, $fullAttrKey, $matches);
            if (count($matches) > 1) {
                $attrKey = $matches[1];
                $attribsToAdd[$attrKey] = $attrVal;
            }
        }

        // Actually do the work of adding attributes to $tag
        if (count($attribsToAdd)) {
            $dom = new \DOMDocument();
            @$dom->loadHTML($tag);
            /** @var \DOMElement[] */
            $resourceTags = $dom->getElementsByTagName($nodeName);
            foreach ($resourceTags as $resourceTagNode) {
                foreach ($attribsToAdd as $attrKey => $attrVal) {
                    $resourceTagNode->setAttribute($attrKey, $attrVal);
                }
            }
            $headStr = $dom->saveHTML($dom->getElementsByTagName('head')[0]);
            // Capture content between <head></head>. Kind of hackish, but should be faster than preg_match
            return substr($headStr, 6, (strlen($headStr) - 14));
        }

        return $tag;
    }
}
