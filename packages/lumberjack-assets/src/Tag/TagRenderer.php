<?php
namespace Adeliom\Lumberjack\Assets\Tag;

use Adeliom\Lumberjack\Assets\Entrypoint\EntrypointLookupInterface;
use Adeliom\Lumberjack\Assets\Entrypoint\IntegrityDataProviderInterface;
use Rareloop\Lumberjack\Facades\Config;
use Rareloop\Lumberjack\Helpers;

class TagRenderer
{
    private $defaultAttributes;
    private $defaultScriptAttributes;
    private $defaultLinkAttributes;

    private $renderedFiles = [];

    public function __construct(
        array $defaultAttributes = [],
        array $defaultScriptAttributes = [],
        array $defaultLinkAttributes = []
    ) {
        $this->defaultAttributes = $defaultAttributes;
        $this->defaultScriptAttributes = array_merge($defaultScriptAttributes, Config::get("assets.script_attributes", []));
        $this->defaultLinkAttributes = array_merge($defaultLinkAttributes, Config::get("assets.link_attributes", []));

        $this->reset();
    }

    public function renderScriptTags(string $entryName, string $packageName = null, array $extraAttributes = []): string
    {
        $scriptTags = [];
        $entryPointLookup = $this->getEntrypointLookup();
        $integrityHashes = ($entryPointLookup instanceof IntegrityDataProviderInterface) ? $entryPointLookup->getIntegrityData() : [];

        foreach ($entryPointLookup->getJavaScriptFiles($entryName) as $filename) {
            $attributes = [];
            $attributes['src'] = $this->getAssetPath($filename, $packageName);
            $attributes = array_merge($attributes, $this->defaultAttributes, $this->defaultScriptAttributes, $extraAttributes);

            if (isset($integrityHashes[$filename])) {
                $attributes['integrity'] = $integrityHashes[$filename];
            }

            $scriptTags[] = sprintf(
                '<script %s></script>',
                $this->convertArrayToAttributes($attributes)
            );

            $this->renderedFiles['scripts'][] = $attributes['src'];
        }

        return implode('', $scriptTags);
    }

    public function renderLinkTags(string $entryName, string $packageName = null, array $extraAttributes = []): string
    {
        $scriptTags = [];
        $entryPointLookup = $this->getEntrypointLookup();
        $integrityHashes = ($entryPointLookup instanceof IntegrityDataProviderInterface) ? $entryPointLookup->getIntegrityData() : [];

        foreach ($entryPointLookup->getCssFiles($entryName) as $filename) {
            $attributes = [];
            $attributes['rel'] = 'stylesheet';
            $attributes['href'] = $this->getAssetPath($filename, $packageName);
            $attributes = array_merge($attributes, $this->defaultAttributes, $this->defaultLinkAttributes, $extraAttributes);

            if (isset($integrityHashes[$filename])) {
                $attributes['integrity'] = $integrityHashes[$filename];
            }

            $scriptTags[] = sprintf(
                '<link %s>',
                $this->convertArrayToAttributes($attributes)
            );

            $this->renderedFiles['styles'][] = $attributes['href'];
        }

        return implode('', $scriptTags);
    }

    public function getRenderedScripts(): array
    {
        return $this->renderedFiles['scripts'];
    }

    public function getRenderedStyles(): array
    {
        return $this->renderedFiles['styles'];
    }

    public function getDefaultAttributes(): array
    {
        return $this->defaultAttributes;
    }

    public function reset()
    {
        $this->renderedFiles = [
            'scripts' => [],
            'styles' => [],
        ];
    }

    private function getAssetPath(string $assetPath, string $packageName = null): string
    {
        $asset = str_replace(parse_url(get_theme_file_uri(), PHP_URL_PATH), "", $assetPath);
        return get_theme_file_uri($asset);
    }

    private function getEntrypointLookup(): EntrypointLookupInterface
    {
        return Helpers::app()->get(sprintf('assets.provider.%s', Config::get("assets.provider", "webpack")));
    }

    private function convertArrayToAttributes(array $attributesMap): string
    {
        // remove attributes set specifically to false
        $attributesMap = array_filter($attributesMap, function($value) {
            return $value !== false;
        });

        return implode(' ', array_map(
            function ($key, $value) {
                // allows for things like defer: true to only render "defer"
                if ($value === true || $value === null) {
                    return $key;
                }

                return sprintf('%s="%s"', $key, htmlentities($value));
            },
            array_keys($attributesMap),
            $attributesMap
        ));
    }
}
