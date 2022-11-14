<?php

namespace Adeliom\Lumberjack\Assets\Provider;

use Adeliom\Lumberjack\Assets\Entrypoint\EntrypointLookup;
use Adeliom\Lumberjack\Assets\Exception\EntrypointNotFoundException;

class WebpackEntrypointLookup extends EntrypointLookup
{
    protected function getEntryFiles(string $entryName, string $key): array
    {
        $this->reset();
        $this->validateEntryName($entryName);
        $entriesData = $this->getEntriesData();
        $entryData = $entriesData['entrypoints'][$entryName] ?? [];

        if (!isset($entryData[$key])) {
            // If we don't find the file type then just send back nothing.
            return [];
        }

        // make sure to not return the same file multiple times
        $entryFiles = $entryData[$key];
        $newFiles = array_values(array_diff($entryFiles, $this->returnedFiles));
        $this->returnedFiles = array_merge($this->returnedFiles, $newFiles);
        return $newFiles;
    }

    private function validateEntryName(string $entryName): void
    {
        $entriesData = $this->getEntriesData();
        if (!isset($entriesData['entrypoints'][$entryName]) && $this->strictMode) {
            $withoutExtension = substr($entryName, 0, strrpos($entryName, '.'));

            if (isset($entriesData['entrypoints'][$withoutExtension])) {
                throw new EntrypointNotFoundException(sprintf('Could not find the entry "%s". Try "%s" instead (without the extension).', $entryName, $withoutExtension));
            }

            throw new EntrypointNotFoundException(sprintf('Could not find the entry "%s" in "%s". Found: %s.', $entryName, $this->entrypointsFile, implode(', ', array_keys($entriesData['entrypoints']))));
        }
    }

    protected function getEntriesData(): array
    {
        $this->entriesData = parent::getEntriesData();

        if (!isset($this->entriesData['entrypoints'])) {
            throw new \InvalidArgumentException(sprintf('Could not find an "entrypoints" key in the "%s" file', $this->entrypointsFile));
        }

        return $this->entriesData;
    }

    public function getIntegrityData(): array
    {
        $entriesData = $this->getEntriesData();

        if (!\array_key_exists('integrity', $entriesData)) {
            return [];
        }

        return $entriesData['integrity'];
    }

    public function entryExists(string $entryName): bool
    {
        $entriesData = $this->getEntriesData();
        return isset($entriesData['entrypoints'][$entryName]);
    }

    public function getAsset(string $ressource): ?string
    {
        $manifestData = $this->getManifestData();

        $withoutLeadingSlash = \ltrim($ressource, '/');
        if (isset($manifestData[$ressource])) {
            return $this->getAssetPath($manifestData[$ressource]);
        }
        if (isset($manifestData[$withoutLeadingSlash])) {
            return $this->getAssetPath($manifestData[$withoutLeadingSlash]);
        }

        return $ressource;
    }
}
