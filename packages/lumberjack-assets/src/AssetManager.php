<?php

namespace Adeliom\Lumberjack\Assets;

use Adeliom\Lumberjack\Assets\Entrypoint\EntrypointLookupInterface;
use Adeliom\Lumberjack\Assets\Wordpress\Enqueuer;
use Rareloop\Lumberjack\Facades\Config;
use Rareloop\Lumberjack\Helpers;

class AssetManager
{

    public function getJsFiles(string $entryName): array
    {
        return $this->getEntrypointLookup()->getJavaScriptFiles($entryName);
    }

    public function getCssFiles(string $entryName): array
    {
        return $this->getEntrypointLookup()->getCssFiles($entryName);
    }

    public function entryExists(string $entryName): bool
    {
        return $this->getEntrypointLookup()->entryExists($entryName);
    }

    public function getAsset(string $ressource): ?string
    {
        return $this->getEntrypointLookup()->getAsset($ressource);
    }

    public function enqueue(string $name, string $entrypoint, array $config = []): array
    {
        return Enqueuer::enqueue($name, $entrypoint, $config);
    }

    private function getEntrypointLookup(): EntrypointLookupInterface
    {
        return Helpers::app()->get(sprintf('assets.provider.%s', Config::get("assets.provider", "webpack")));
    }
}
