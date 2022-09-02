<?php

namespace Adeliom\Lumberjack\Assets\Entrypoint;

use Adeliom\Lumberjack\Assets\Exception\EntrypointNotFoundException;

abstract class EntrypointLookup implements EntrypointLookupInterface, IntegrityDataProviderInterface
{
    protected $manifestPath;

    protected $entriesData;

    protected $returnedFiles = [];

    protected $strictMode;

    public function __construct(string $manifestPath, bool $strictMode = true)
    {
        $this->manifestPath = $manifestPath;
        $this->strictMode = $strictMode;
    }

    abstract protected function getEntryFiles(string $entryName, string $key): array;
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

        if (!file_exists($this->manifestPath)) {
            if (!$this->strictMode) {
                return [];
            }
            throw new \InvalidArgumentException(sprintf('Could not find the entrypoints file from Webpack: the file "%s" does not exist.', $this->manifestPath));
        }

        $this->entriesData = json_decode(file_get_contents($this->manifestPath), true);

        if (null === $this->entriesData) {
            throw new \InvalidArgumentException(sprintf('There was a problem JSON decoding the "%s" file', $this->manifestPath));
        }

        return $this->entriesData;
    }
}
