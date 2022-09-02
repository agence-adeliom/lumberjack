<?php

namespace Adeliom\Lumberjack\Assets;

use Blast\Facades\AbstractFacade;


/**
 * @method static array getJsFiles(string $entryName)
 * @method static array getCssFiles(string $entryName)
 * @method static bool entryExists(string $entryName)
 * @method static string getAsset(string $ressource)
 * @method static array enqueue(string $name, string $entryName, array $config)
 */
class Assets extends AbstractFacade
{
    public static function accessor(): string
    {
        return 'assets';
    }
}
