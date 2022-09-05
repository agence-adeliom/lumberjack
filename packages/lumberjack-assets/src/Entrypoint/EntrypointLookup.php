<?php

namespace Adeliom\Lumberjack\Assets\Entrypoint;

use Adeliom\Lumberjack\Assets\Exception\EntrypointNotFoundException;
use Rareloop\Lumberjack\Facades\Config as ConfigFacade;

abstract class EntrypointLookup implements EntrypointLookupInterface, IntegrityDataProviderInterface
{
    public const ENTRYPOINTS_FILE_NAME = 'entrypoints.json';
    public const MANIFEST_FILE_NAME = 'manifest.json';

    protected $entrypointsFileName;
    protected $manifestFileName;

    protected $entrypointsFile;
    protected $manifestFile;

    protected $buildPath;

    protected $entriesData;
    protected $manifestData;

    protected $returnedFiles = [];

    protected $strictMode;

    public function __construct(string $buildPath, bool $strictMode = true)
    {
        $this->entrypointsFileName = ConfigFacade::get("assets.entrypoints_file_name", self::ENTRYPOINTS_FILE_NAME);
        $this->manifestFileName = ConfigFacade::get("assets.manifest_file_name", self::MANIFEST_FILE_NAME);

        $this->entrypointsFile = preg_replace('#/+#', '/', sprintf('%s/%s/%s', get_template_directory(), $buildPath, $this->entrypointsFileName));
        $this->manifestFile = preg_replace('#/+#', '/', sprintf('%s/%s/%s', get_template_directory(), $buildPath, $this->manifestFileName));

        $this->buildPath = $buildPath;
        $this->strictMode = $strictMode;
    }

    abstract protected function getEntryFiles(string $entryName, string $key): array;
    abstract public function getAsset(string $ressource): ?string;
    abstract public function entryExists(string $entryName): bool;

    public function getJavaScriptFiles(string $entryName): array
    {
        return $this->getEntryFiles($entryName, 'js');
    }

    public function getCssFiles(string $entryName): array
    {
        return $this->getEntryFiles($entryName, 'css');
    }

    public function getIntegrityData(): array
    {
        return [];
    }
    /**
     * Resets the state of this service.
     */
    public function reset()
    {
        $this->returnedFiles = [];
    }

    protected function getEntriesData(): array
    {
        if (null !== $this->entriesData) {
            return $this->entriesData;
        }

        if (!file_exists($this->entrypointsFile)) {
            if (!$this->strictMode) {
                return [];
            }
            throw new \InvalidArgumentException(sprintf('Could not find the entrypoints file: the file "%s" does not exist.', $this->entrypointsFile));
        }

        $this->entriesData = json_decode(file_get_contents($this->entrypointsFile), true);

        if (null === $this->entriesData) {
            throw new \InvalidArgumentException(sprintf('There was a problem JSON decoding the "%s" file', $this->entrypointsFile));
        }

        return $this->entriesData;
    }

    protected function getManifestData(): array
    {
        if (null !== $this->manifestData) {
            return $this->manifestData;
        }

        if (!file_exists($this->manifestFile)) {
            if (!$this->strictMode) {
                return [];
            }
            throw new \InvalidArgumentException(sprintf('Could not find the manifest file: the file "%s" does not exist.', $this->manifestFile));
        }

        $this->manifestData = json_decode(file_get_contents($this->manifestFile), true);

        if (null === $this->manifestData) {
            throw new \InvalidArgumentException(sprintf('There was a problem JSON decoding the "%s" file', $this->manifestData));
        }

        return $this->manifestData;
    }

    protected function getAssetPath(string $assetPath): string
    {
        $asset = str_replace(parse_url(get_theme_file_uri(), PHP_URL_PATH), "", $assetPath);
        return get_theme_file_uri($asset);
    }
}
