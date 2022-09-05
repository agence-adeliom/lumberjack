<?php

namespace Adeliom\Lumberjack\Assets\Entrypoint;

use Adeliom\Lumberjack\Assets\Exception\EntrypointNotFoundException;

interface EntrypointLookupInterface
{
    /**
     * @throws EntrypointNotFoundException if an entry name is passed that does not exist in entrypoints file
     */
    public function getJavaScriptFiles(string $entryName): array;

    /**
     * @throws EntrypointNotFoundException if an entry name is passed that does not exist in entrypoints file
     */
    public function getCssFiles(string $entryName): array;

    /**
     * Check if the entry name passed exist in entrypoints file
     */
    public function entryExists(string $entryName): bool;

    public function getAsset(string $ressource): ?string;
}
